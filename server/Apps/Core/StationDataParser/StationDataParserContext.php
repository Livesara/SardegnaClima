<?php
namespace StationDataParser;
require_once __DIR__ ."/../../../bootstrap.php";
class StationDataParserContext{
	private $strategy = null;
	public function __construct($station, $filter) {
        if(!count($filter) || in_array( $station->getType(), $filter)) {
            \Logger::getLogger('parser')->trace("station type: " . $station->getType());
            \Logger::getLogger('parser')->trace("station data_source: " . $station->getDataUrl());
            switch ($station->getType()) {
                case "CLIENTRAW":
                    $this->strategy = new ClientRawParser($station);
                    break;
                case "SCLIMA":
                    $this->strategy = new SClimaParser($station);
                    break;
                case "WUNDER":
                    $this->strategy = new WundergroundParser($station);
                    break;
                case "WL_LIVEMAP":
                    $this->strategy = new WLLiveMapParser($station);
                    break;
                case "TAGMER":
                    $this->strategy = new TagmerParser($station);
                    break;
                case "DATI_STAZIONE":
                    $this->strategy = new StationDataParser($station);
                    break;
                case "DOWNLD02":
                    $this->strategy = new Downld02Parser($station);
                    break;
                case "DOWNLD02A":
                    $this->strategy = new Downld02AParser($station);
                    break;
                case "LOGDAT":
                    $this->strategy = new LogDatParser($station);
                    break;
                case "CURRDATA":
                    $this->strategy = new CurrDataParser($station);
                    break;
                case "REALTIME":
                    $this->strategy = new RealTimeParser($station);
                    break;
                case "WLINK":
                    $this->strategy = new WlinkParser($station);
                    break;
                case "WLINK2":
                    $this->strategy = new Wlink2Parser($station);
                    break;
    
            }
        }
        else
            return null;
	}
	public function getMeasure($data_url){
        \Logger::getLogger('parser')->trace("getMeasure()");
        global $config;
        if($this->strategy)
            try{
                $measure = null;
                $attempts = $config['attempts'];
                for($i = 0; $i < $attempts && !$measure; $i++ ) {
                    \Logger::getLogger('parser')->trace("attempt #" . $i);
                    $measure = $this->strategy->getMeasure($data_url);
                    if(!$measure) usleep($config['attemptsInterval']);
                }
                if($measure == "DONE") return "DONE";
                // filter too old measure
                $date = new \DateTime();
                $date->modify('-2 hours');
                if($measure && $measure->getDate() > $date){
                    return $measure;
                }
                else{
                    \Logger::getLogger('parser')->error("expired measure" );
                    return null;
                }
            }catch(\Exception $e){
                \Logger::getLogger('parser')->error($e->getMessage());
                return null;
            }
        else
            return "DONE";
	}
}
