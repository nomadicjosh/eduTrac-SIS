<?php
/*
 * DropIn Name: Email Address Encoder
 * Description: A plugin which protects email addresses from email-harvesting robots by encoding them into decimal and hexadecimal entities.
 */
$app = \Liten\Liten::getInstance();

if (! defined('EAE_FILTER_PRIORITY'))
    define('EAE_FILTER_PRIORITY', 1000);

foreach (array(
    'the_custom_page_content',
    'the_myetsis_page_content',
    'the_myetsis_welcome_message'
) as $filter) {
    $app->hook->add_filter($filter, 'eae_encode_emails', EAE_FILTER_PRIORITY);
}

/**
 * Searches for plain email addresses in given $string and
 * encodes them (by default) with the help of eae_encode_str().
 *
 * Regular expression is based on based on John Gruber's Markdown.
 * http://daringfireball.net/projects/markdown/
 *
 * @param string $string
 *            Text with email addresses to encode
 * @return string $string Given text with encoded email addresses
 */
function eae_encode_emails($string)
{
    $app = \Liten\Liten::getInstance();
    // abort if $string doesn't contain a @-sign
    if ($app->hook->apply_filter('eae_at_sign_check', true)) {
        if (strpos($string, '@') === false)
            return $string;
    }
    
    // override encoding function with the 'eae_method' filter
    $method = $app->hook->apply_filter('eae_method', 'eae_encode_str');
    
    // override regex pattern with the 'eae_regexp' filter
    $regexp = $app->hook->apply_filter('eae_regexp', '{
			(?:mailto:)?
			(?:
				[-!#$%&*+/=?^_`.{|}~\w\x80-\xFF]+
			|
				".*?"
			)
			\@
			(?:
				[-a-z0-9\x80-\xFF]+(\.[-a-z0-9\x80-\xFF]+)*\.[a-z]+
			|
				\[[\d.a-fA-F:]+\]
			)
		}xi');
    
    return preg_replace_callback($regexp, create_function('$matches', 'return ' . $method . '($matches[0]);'), $string);
}

/**
 * Encodes each character of the given string as either a decimal
 * or hexadecimal entity, in the hopes of foiling most email address
 * harvesting bots.
 *
 * Based on Michel Fortin's PHP Markdown:
 * http://michelf.com/projects/php-markdown/
 * Which is based on John Gruber's original Markdown:
 * http://daringfireball.net/projects/markdown/
 * Whose code is based on a filter by Matthew Wickline, posted to
 * the BBEdit-Talk with some optimizations by Milian Wolff.
 *
 * @param string $string
 *            Text with email addresses to encode
 * @return string $string Given text with encoded email addresses
 */
function eae_encode_str($string)
{
    $chars = str_split($string);
    $seed = mt_rand(0, (int) abs(crc32($string) / strlen($string)));
    
    foreach ($chars as $key => $char) {
        
        $ord = ord($char);
        
        if ($ord < 128) { // ignore non-ascii chars
            $r = ($seed * (1 + $key)) % 100; // pseudo "random function"
            
            if ($r > 60 && $char != '@'); // plain character (not encoded), if not @-sign
            else 
                if ($r < 45)
                    $chars[$key] = '&#x' . dechex($ord) . ';'; // hexadecimal
                else
                    $chars[$key] = '&#' . $ord . ';'; // decimal (ascii)
        }
    }
    
    return implode('', $chars);
}
