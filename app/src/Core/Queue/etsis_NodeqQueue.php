<?php namespace app\src\Core\Queue;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\Exception;
use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\NodeQ\Helpers;
use Cascade\Cascade;

/**
 * NodeQ Task Manager Queue
 *  
 * @since       6.3.4
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class etsis_NodeqQueue implements etsis_ReliableQueueInterface, etsis_QueueGarbageCollectionInterface
{

    /**
     * The name of the queue this instance is working with.
     *
     * @var string
     */
    protected $name;

    /**
     * How long the processing is expected to take in seconds.
     */
    protected $lease_time;

    /**
     * Send NodeQ Queue internal messages to 'etsis-error*.txt'
     */
    protected $debug;

    /**
     * When should the process run.
     */
    protected $schedule = '* * * * *';

    /**
     * The nodeq table name.
     */
    public $node = 'etsis_queue';

    /**
     * Application object.
     * 
     * @var object
     */
    public $app;

    /**
     * Constructs a \Liten\Liten $liten object.
     *
     * @param array $config
     *   The name of the queue.
     * @param \Liten\Liten $liten
     *   Liten framework object.
     */
    public function __construct(array $config = [], \Liten\Liten $liten = null)
    {
        $this->name = $config['name'];
        $this->lease_time = $config['lease_time'];
        $this->schedule = $config['schedule'];
        $this->debug = (bool) $config['debug'];
        $this->app = !empty($liten) ? $liten : \Liten\Liten::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        $try_again = false;
        try {
            $id = $this->doCreateItem($data);
        } catch (NodeQException $e) {
            /**
             * If there was an exception, try to create the node.
             */
            if (!$try_again = $this->ensureNodeExists()) {
                /**
                 * If the exception happened for other reason than the missing node,
                 * propagate the exception.
                 */
                throw $e;
            }
        }
        /**
         * Now that the node has been created, try again if necessary.
         */
        if ($try_again) {
            $id = $this->doCreateItem($data);
        }
        return $id;
    }

    /**
     * Adds a queue item and store it directly to the queue.
     *
     * @param $data
     *   Arbitrary data to be associated with the new task in the queue.
     *
     * @return
     *   A unique ID if the item was successfully created and was (best effort)
     *   added to the queue, otherwise false. We don't guarantee the item was
     *   committed to disk etc, but as far as we know, the item is now in the
     *   queue.
     */
    protected function doCreateItem($data)
    {
        $scheduleChecker = new \Jobby\ScheduleChecker();
        /**
         * Check if queue is due or not due.
         */
        if (!$scheduleChecker->isDue($this->schedule)) {
            return false;
        }
        $query = Node::table($this->node);
        $query->name = (string) $this->name;
        $query->data = (string) maybe_serialize($data);
        /**
         * We cannot rely on REQUEST_TIME because many items might be created
         * by a single request which takes longer than 1 second.
         */
        $query->created = (int) time();
        $query->save();
        /**
         * Return the new serial ID, or false on failure.
         */
        return $query->lastId();
    }

    /**
     * {@inheritdoc}
     */
    public function numberOfItems()
    {
        try {
            return Node::table($this->node)->where('name', '=', $this->name)->count();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE: %s', $e->getMessage()), ['Queue' => 'Queue::numberOfItems']);
            /**
             * If the node does not exist there are no items currently
             * available to claim.
             */
            return false;
        } catch (Exception $e) {
            $this->catchException($e);
            /**
             * If there is no node there cannot be any items.
             */
            return 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function claimItem($lease_time = 30)
    {
        /**
         * Claim an item by updating its expire fields. If claim is not
         * successful another thread may have claimed the item in the meantime.
         * Therefore loop until an item is successfully claimed or we are
         * reasonably sure there are no unclaimed items left.
         */
        while (true) {
            try {
                $item = Node::table($this->node)
                    ->where('expire', '=', 0)
                    ->andWhere('name', '=', $this->name)
                    ->orderBy('created')
                    ->orderBy('id')
                    ->find();
            } catch (NodeQException $e) {
                Cascade::getLogger('error')->error(sprintf('NODEQSTATE: %s', $e->getMessage()), ['Queue' => 'Queue::claimItem Query']);
                /**
                 * If the node is empty or false, there are no items currently
                 * available to claim.
                 */
                return false;
            } catch (Exception $e) {
                $this->catchException($e);
                /**
                 * If the node does not exist there are no items currently
                 * available to claim.
                 */
                return false;
            }
            if ($item) {
                try {
                    /**
                     * Try to update the item. Only one thread can succeed in
                     * UPDATEing the same row. We cannot rely on REQUEST_TIME
                     * because items might be claimed by a single consumer which
                     * runs longer than 1 second. If we continue to use REQUEST_TIME
                     * instead of the current time(), we steal time from the lease,
                     * and will tend to reset items before the lease should really
                     * expire.
                     */
                    $update = Node::table($this->node)->where('expire', '=', 0)->find($item->id);
                    $update->expire = time() + ($this->lease_time <= 0 ? $lease_time : $this->lease_time);
                    /**
                     * If there are affected rows, this update succeeded.
                     */
                    $update->save();
                    return $item;
                } catch (NodeQException $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE: %s', $e->getMessage()), ['Queue' => 'Queue::claimItem Update']);
                    /**
                     * If the node does not exist there are no items currently
                     * available to claim.
                     */
                    return false;
                } catch (Exception $e) {
                    $this->catchException($e);
                    /**
                     * If the node does not exist there are no items currently
                     * available to claim.
                     */
                    return false;
                }
            } else {
                /**
                 * No items currently available to claim.
                 */
                return false;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function releaseItem($item)
    {
        try {
            $update = Node::table($this->node)->find($item->id);
            $update->expire = 0;
            return $update->save();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE: %s', $e->getMessage()), ['Queue' => 'Queue::releaseItem']);
            /**
             * If the node does not exist there are no items currently
             * available to claim.
             */
            return false;
        } catch (Exception $e) {
            $this->catchException($e);
            /**
             * If the node doesn't exist we should consider the item released.
             */
            return true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($item)
    {
        try {
            Node::table($this->node)
                ->find($item->id)
                ->delete();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE: %s', $e->getMessage()), ['Queue' => 'Queue::deleteItem']);
            /**
             * If the node does not exist there are no items currently
             * available to claim.
             */
            return false;
        } catch (Exception $e) {
            $this->catchException($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createQueue()
    {
        /**
         * All tasks are stored in a single node (which is created on
         * demand) so there is nothing we need to do to create a new queue.
         */
    }

    /**
     * {@inheritdoc}
     */
    public function deleteQueue()
    {
        try {
            Node::table($this->node)
                ->where('name', '=', $this->name)
                ->find()
                ->delete();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE: %s', $e->getMessage()), ['Queue' => 'Queue::deleteQueue']);
            /**
             * If the node does not exist there are no items currently
             * available to claim.
             */
            return false;
        } catch (Exception $e) {
            $this->catchException($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function garbageCollection()
    {
        try {
            /**
             * Clean up the queue for failed batches.
             */
            Node::table($this->node)
                ->where('created', '<', REQUEST_TIME - 864000)
                ->andWhere('name', '=', $this->name)
                ->find()
                ->delete();
            /**
             * Reset expired items in the default queue implementation node. If
             * that's not used, this will simply be a no-op.
             */
            $update = Node::table($this->node)
                ->where('expire', '<>', 0)
                ->andWhere('expire', '<', REQUEST_TIME);
            $update->expire = 0;
            $update->save();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE: %s', $e->getMessage()), ['Queue' => 'Queue::garbageCollection']);
            /**
             * If the node does not exist there are no items currently
             * available to claim.
             */
            return false;
        } catch (Exception $e) {
            $this->catchException($e);
        }
    }

    /**
     * Check if the node exists and create it if not.
     */
    protected function ensureNodeExists()
    {
        try {
            if (!Helpers\Validate::table($this->node)->exists()) {
                $this->schemaDefinition();
                return true;
            }
        }
        /**
         * If another process has already created the queue node, attempting to
         * recreate it will throw an exception. In this case just catch the
         * exception and do nothing.
         */ catch (NodeQException $e) {
            return true;
        }
        return false;
    }

    /**
     * Act on an exception when queue might be stale.
     *
     * If the node does not yet exist, that's fine, but if the node exists and
     * yet the query failed, then the queue is stale and the exception needs to
     * propagate.
     *
     * @param $e
     *   The exception.
     *
     * @throws Exception
     *   If the node exists the exception passed in is rethrown.
     */
    protected function catchException(Exception $e)
    {
        if (Helpers\Validate::table($this->node)->exists()) {
            throw $e;
        }
    }

    public function executeAction($data)
    {
        /**
         * At start of executing the action.
         */
        $time_start = microtime(true);
        /**
         * The action that should run when queue is called.
         */
        $this->app->hook->do_action($data['action_hook']);
        /**
         * At the end of executing the action.
         */
        $time_end = (microtime(true) - $time_start);
        try {
            $task = Node::table('tasks')->where('pid', '=', $data['pid'])->find();
            $task->executions = $task->executions + 1;
            $task->lastrun = (string) \Jenssegers\Date\Date::now()->format('Y-m-d h:i:s');
            $task->last_runtime = (double) $time_end;
            $task->save();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE: %s', $e->getMessage()), ['Queue' => 'Queue::executeAction']);
        } catch (Exception $e) {
            $this->catchException($e);
        }
        return true;
    }

    /**
     * Defines the schema for the queue node.
     */
    public function schemaDefinition()
    {
        try {
            Node::create($this->node, [
                'name' => 'string',
                'data' => 'string',
                'expire' => 'integer',
                'created' => 'integer',
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Queue' => 'Queue::schemaDefinition']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Queue' => 'Queue::schemaDefinition']);
        }
    }
}
