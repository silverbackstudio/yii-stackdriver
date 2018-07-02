<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Logging\LoggingClient;
use Google\Cloud\Core\Report\SimpleMetadataProvider;

class StackdriverLogRoute extends CEmailLogRoute
{
    
    public $logName = 'application-log';
    public $serviceName = 'application';
    public $serviceVersion = '1.0';
    
    protected $metadataProvider = null;
    protected $psrLogger = null;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $loggingClient = new LoggingClient();

        $this->metadataProvider = new SimpleMetadataProvider([], '', $this->serviceName, $this->serviceVersion);

        $this->psrLogger = $loggingClient->psrLogger($this->logName, [
            'batchEnabled' => true,
            'metadataProvider' => $this->metadataProvider,
            'batchOptions' => [
                'numWorkers' => 2
            ]
        ]);
    }    
    
	/**
	 * Sends log messages to Stackdriver.
	 * @param array $logs list of log messages
	 */
	protected function processLogs($logs)
	{
	    
		foreach($logs as $log) {
		    
		    $context = array( 
		        'category' => $log[2]
		    );
		    
            $app = Yii::app();
            
            if($app instanceof CWebApplication) {
    		    $context['httpRequest'] = array(
    		        'requestUrl' => $app->request->url,
    		        'requestMethod' => $app->request->requestType,
    		    );
            }
		    
			$this->psrLogger->log( $log[1], $this->formatLogMessage($log[0],$log[1],$log[2],$log[3]), $context );
		}

	}
    
}