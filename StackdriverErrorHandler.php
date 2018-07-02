<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Logging\LoggingClient;
use Google\Cloud\Core\Report\SimpleMetadataProvider;

/**
 * Extending CErrorHandler to integrate with New Relic errors
 */
class StackdriverErrorHandler extends CErrorHandler
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
        
        Google\Cloud\ErrorReporting\Bootstrap::init( $this->psrLogger );

    }    
    
	/**
	 * @inheritdoc
	 */
	protected function handleException($exception)
	{
        
        Google\Cloud\ErrorReporting\Bootstrap::exceptionHandler( $exception ); 

        parent::handleException( $exception );
	}
	
	/**
	 * @inheritdoc
	 */
	protected function handleError( $event )
	{
        
        Google\Cloud\ErrorReporting\Bootstrap::errorHandler( $event->code, $event->message, $event->file, $event->line ); 

        parent::handleError( $event );
	}	
}