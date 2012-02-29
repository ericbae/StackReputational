<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Extend system/helper/date_helper.php 
 * CUSTOM! Got it from CodeIgniter website to display the relative time elapsed (e.g. 2 min ago, 1 month ago etc)
 */
if ( ! function_exists('date_when') )
{
    function date_when( $timestamp = NULL, $base = NULL)
    {
        if( strlen($timestamp) < 10 ) return;
        
        if( empty($base) ) $base = now();
        
        // Is timestamp in past or future?
        $past = ($base > $timestamp) ? TRUE : FALSE;
        //$past = ($base > $timestamp); 
        
        // Create suffix based on past/future
        //$suffix = $end = ($past) ? ' ago' : ' from now';
        $suffix = ($past) ? ' ago' : ' from now'; 
        
        // Actual time string of timestamp ie 4:54 pm
        $timestr = date('g:ia',$timestamp);
        
        $diff = abs($timestamp - $base);
        $periods = array('year'=>31536000,'month'=>2628000,'day'=>86400,'hour'=>3600,'minute'=>60,'second'=>1);
        
        // create array holding count of each period
        
        $out = array();
        
        foreach($periods as $period => $seconds)
        {
            if( $diff > $seconds )
            {
                $result = floor($diff/$seconds);
                $diff =  $diff % $seconds;
                $out[] = array($period, $result);
            }
        }
        
        // Get largest period, other counts are still in $out for use
        $top = array_shift($out);
        
        switch($top[0])
        {
            case 'month' :
                $output = $top[1] == 1 ? ( $past ? 'last month' : 'next month' ) : $top[1] . ' months' . $suffix;
                break;
            case 'day' :
                $output = $top[1] == 1 ? ( $past ? 'yesterday' : 'tomorrow' ) .' '. $timestr : $top[1] . ' days' . $suffix;
                break;
            case 'hour':
                // Calculate in case, for example if yesterday was only 7 hours ago
                $output = date('j',$base) == date('j',$timestamp) ? 'today '.$timestr : (( $past ? 'yesterday' : 'tomorrow' ) . ' '.$timestr);
                break;
            default :
                $output = $top[1] .' '. $top[0] . ( $top[1] > 1 ? 's' : '' ) . $suffix;
                // for 0 second, added by Xiangwei on 05/01/2009
                if (($top[0] == 'second' OR $top[0] == '') AND $top[1] == 0)
                {
                	$output = " just now";
                }
                break;
        }
        return $output;        
    }
} 

/*
 * Below is the function that returns the server time to the timezone I want!
 */
if ( ! function_exists('get_aest_hour') )
{
	function get_curr_timestamp()
	{
		$timezone = new DateTimeZone( "Australia/Sydney" );
		$date = new DateTime();
		$date->setTimezone( $timezone );
		$date_string = $date->format( 'Y-m-d H:i:s' );
		//return strtotime($date_string)+3600;
		return strtotime($date_string);
	} 
}

/* End of file MY_date_helper.php */
/* Location: ./system/application/helper/MY_date_helper.php */