<?php
namespace App\Logging;
// use Illuminate\Log\Logger;
use DB;
use Illuminate\Support\Facades\Auth;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Request;
use Jenssegers\Agent\Agent;

class MySQLLoggingHandler extends AbstractProcessingHandler{
/**
 *
 * Reference:
 * https://github.com/markhilton/monolog-mysql/blob/master/src/Logger/Monolog/Handler/MysqlHandler.php
 */
    public function __construct($level = Logger::DEBUG, $bubble = true) {
        $this->table = 'data_log';
        parent::__construct($level, $bubble);
    }
    protected function write(array $record):void
    {
       // dd($record);   
     $agent = new Agent();
     $browser = $agent->browser();
     $version_browser = $agent->version($browser);

     $platform = $agent->platform();
     $version_platform = $agent->version($platform);

       $data = array(
           'message'       => $record['message'],
           'context'       => json_encode($record['context']),
           'level'         => $record['level'],
           'level_name'    => $record['level_name'],
           'channel'       => $record['channel'],
           'record_datetime' => $record['datetime']->format('Y-m-d H:i:s'),
           'extra'         => json_encode($record['extra']),
           'formatted'     => $record['formatted'],
           'remote_addr'   => Request::ip(),
           'device'        => $platform.' '.$version_platform,
           'user_agent'    => $browser.' '.$version_browser,
           'created_at'    => date("Y-m-d H:i:s"),
           'user_id'       => isset(\Auth::user()->id)?\Auth::user()->id:null,
       );
       DB::connection()->table($this->table)->insert($data);     
    }
}