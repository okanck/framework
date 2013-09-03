## Log Helper

The Log Helper assist you write messages to your log files.

#### log\me('level', 'message')

This function lets you write messages to your log files. You must supply one of three "levels" in the first parameter, indicating what type of message it is (debug, error, info), with the message itself in the second parameter. Example:

```php
if ($some_var == "")
{
    log\me('error', 'Some variable did not contain a value.');
}
else
{
    log\me('debug', 'Some variable was correctly set');
}

log\me('info', 'The purpose of some variable is to provide some value.');
```

<ol>
There are three message types:

    <li>Error Messages. These are actual errors, such as PHP errors or user errors.</li>
    <li>Debug Messages. These are messages that assist in debugging. For example, if a class has been initialized, you could log this as debugging info.</li>
    <li>Informational Messages. These are the lowest priority messages, simply giving information regarding some process. Obullo doesn't natively generate any info messages but you may want to in your application.</li></ol>

**Note:** In order for the log file to actually be written, the "logs" folder must be writable which is located at <dfn>app/core/logs<dfn>. In addition, you must set the "threshold" for logging. You might, for example, only want error messages to be logged, and not the other two types. If you set it to zero logging will be disabled. (Look at <dfn>app/config/config.php</dfn>)

#### log\me('level', '[ module ]: message')

If you want to keep logs in different color use "[ ? ]" symbols before the start of the log message.

```php
log\me('debug', '[ welcome ]: Example message.');
```
#### Debugging to console

You can follow the all log messages using below the command.

```php
obm log
```
You can set the log level filter using the level argument.

```php
obm log level info
```

This command display only log messages which are flagged as debug.

```php
obm log level debug
```