<?php namespace app\src\Core;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\Exception;
use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\IOException;
use Cascade\Cascade;

/**
 * Task Manager Queue
 *  
 * @since       6.3.4
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Queue
{

    /**
     * Application object.
     * 
     * @var object
     */
    public $app;

    /**
     * @var array
     */
    public $config = [];

    /**
     * @var array
     */
    protected $jobs = [];

    /**
     * Node where queues are saved.
     * 
     * @var type 
     */
    public $node = 'tasks';

    /**
     * Set the directory for where pid is found.
     * 
     * @var type 
     */
    public $dir = '';

    /**
     * ID of the running process.
     * 
     * @var type 
     */
    public $pid = 0;

    /**
     * 
     * @param \Liten\Liten $liten
     */
    public function __construct(array $config = [], \Liten\Liten $liten = null)
    {
        if (!NodeQ\Helpers\Validate::table($this->node)->exists()) {
            Node::dispense($this->node);
        }

        $this->setConfig($this->getDefaultConfig());
        $this->setConfig($config);

        $this->app = !empty($liten) ? $liten : \Liten\Liten::getInstance();

        try {
            /**
             * Creates a directory with proper permissions.
             */
            _mkdir($this->app->config('file.savepath') . 'etsis_queue');
        } catch (IOException $e) {
            Cascade::getLogger('error')->error(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()));
            Cascade::getLogger('system_email')->alert(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()));
        }
        $this->dir = $this->app->config('file.savepath') . 'etsis_queue' . DS;
    }

    /**
     * @return array
     */
    public function getDefaultConfig()
    {
        return [
            'task_callback' => null,
            'action_hook' => null,
            'schedule' => \Jenssegers\Date\Date::now(),
            'max_runtime' => null,
            'enabled' => true,
            'debug' => false,
        ];
    }

    /**
     * @param array
     */
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function jobs()
    {
        return $this->jobs;
    }

    /**
     * Add a job.
     *
     * @param string $job
     * @param array  $config
     *
     * @throws Exception
     */
    public function add($job, array $config)
    {
        if (empty($config['schedule'])) {
            throw new Exception("'schedule' is required for '$job' job", 8176);
        }

        if (empty($config['task_callback'])) {
            throw new Exception("'task_callback' is required for '$job' job", 8662);
        }

        if (!function_exists($config['task_callback'])) {
            throw new Exception("'task_callback' must exist as a function", 8662);
        }

        if (empty($config['action_hook'])) {
            throw new Exception("'action_hook' is required for '$job' job", 8465);
        }

        $config = array_merge($this->config, $config);
        $this->jobs[$job] = $config;
    }

    public function node()
    {
        return $this->node;
    }

    /**
     * Create a new job and save it to the queue or update the job if it exists.
     * 
     * @since 6.3.4
     */
    public function enqueue($args)
    {
        $tasks = etsis_parse_args($args);

        try {
            $count = Node::table($this->node())->where('pid', '=', $tasks['task_worker']['pid']);
            if ($count->findAll()->count() >= 1) {
                $node = Node::table($this->node())->where('pid', '=', $tasks['task_worker']['pid'])->find();
                foreach ($tasks['task_worker'] as $k => $v) {
                    $node->$k = $v;
                }
                $node->save();
            } else {
                $node = Node::table($this->node());
                foreach ($tasks['task_worker'] as $k => $v) {
                    $node->$k = $v;
                }
                $node->save();
            }
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('QUEUESTATE[8684]: %s', $e->getMessage()));
        } catch (\InvalidArgumentException $e) {
            Cascade::getLogger('error')->error(sprintf('QUEUESTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('QUEUESTATE[2646]: %s', $e->getMessage()));
        }
    }

    public function getMyPid()
    {
        return $this->pid;
    }

    /**
     * @param string $lockFile
     * @param array $config
     * @throws Exception
     */
    protected function checkMaxRuntime($lockFile, $config)
    {
        $max_runtime = $config['max_runtime'];
        if ($max_runtime === null) {
            return;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            throw new Exception('"max_runtime" is not supported on Windows.', 8712);
        }

        $runtime = $this->getLockLifetime($lockFile);
        if ($runtime < $max_runtime) {
            return;
        }

        throw new Exception("Max Runtime of $max_runtime secs exceeded! Current runtime: $runtime secs.", 8712);
    }

    /**
     * @param string $lockFile
     * @return int
     */
    public function getLockLifetime($lockFile)
    {
        if (!file_exists($lockFile)) {
            return 0;
        }

        $pid = _file_get_contents($lockFile);
        if (!empty($pid)) {
            return 0;
        }

        $stat = stat($lockFile);

        return (time() - $stat["mtime"]);
    }

    public function releaseLockFile($lockFile)
    {
        @unlink($lockFile);
        if (!file_exists($lockFile)) {
            $fh = fopen($lockFile, 'a');
            fclose($fh);
        }
    }

    public function run()
    {
        $scheduleChecker = new \Jobby\ScheduleChecker();
        foreach ($this->jobs as $config) {
            /**
             * The queue's lock file.
             */
            $lockFile = $this->dir . $config['pid'];
            /**
             * Check if queue is due or not due.
             */
            if (!$scheduleChecker->isDue($config['schedule'])) {
                continue;
            }
            /**
             * Deletes and recreates the queue's lock file.
             */
            $this->releaseLockFile($lockFile);
            /**
             * If config is not set or is false,
             * do not continue
             */
            if (!$config['enabled'] || $config['enabled'] == false) {
                continue;
            }
            /**
             * Checks max runtime.
             */
            try {
                $this->checkMaxRuntime($lockFile, $config);
            } catch (Exception $e) {
                if ($config['debug']) {
                    Cascade::getLogger('error')->error(sprintf('QUEUESTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['PID' => $config['pid'], 'Queue' => $config['name']]);
                    Cascade::getLogger('system_email')->alert(sprintf('QUEUESTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['PID' => $config['pid'], 'Queue' => $config['name']]);
                }
                return;
            }
            /**
             * At start of executing the action.
             */
            $time_start = microtime(true);
            /**
             * The action that should run when queue is called.
             */
            $this->app->hook->do_action($config['action_hook']);
            /**
             * At the end of executing the action.
             */
            $time_end = (microtime(true) - $time_start);
            try {
                /**
                 * Update the queue's # of runs as well as the last
                 * time it ran.
                 */
                $upd = Node::table('tasks')->where('pid', '=', $config['pid'])->find();
                $upd->executions = $upd->executions + 1;
                $upd->lastrun = (string) \Jenssegers\Date\Date::now()->format('Y-m-d h:i:s');
                $upd->last_runtime = (double) $time_end;
                $upd->save();
            } catch (NodeQException $e) {
                Cascade::getLogger('error')->error(sprintf('QUEUESTATE[8684]: %s', $e->getMessage()));
            } catch (\InvalidArgumentException $e) {
                Cascade::getLogger('error')->error(sprintf('QUEUESTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error(sprintf('QUEUESTATE[2646]: %s', $e->getMessage()));
            }
        }
    }
}
