# Yii 1.x - Google Cloud (Stackdriver) Monitoring 

## Installation

Clone this repository in `protected/extensions/yii-stackdriver`.

Launch a `composer update` to download required dependencies.

## Config

Add the log route to CLogRouter to pipe logs in Google Cloud Logging.

```php

'log'=>array(
	'class'=>'CLogRouter',
	'routes'=>array(
		array(
			'class'=>'ext.yii-stackdriver.StackdriverLogRoute',
			'levels'=>'error, warning, info, profile, debug',
			
			// override error severity for some exceptions
			'errorSeverity' => array(
				'exception.CHttpException.404' => 'info',
			)
		),
		...
	)
)

```

Customize the class for error handling in config.

```php
'errorHandler'=>array(
	// use 'site/error' action to display errors
	'class'=>'ext.yii-stackdriver.StackdriverErrorHandler',
	'errorAction'=>'site/error',
	
	// do not consider 404 as errors/exceptions
	'skip404' => true,
),
```

Authorize the GCE VM service account with the following privileges:

* Error Reporting Author
* Logs Author
