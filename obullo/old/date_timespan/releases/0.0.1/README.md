## Date_Timespan Class

------

The Date Timespan Class contains functions that help you work with dates.

### Initializing the Class

------

```php
new Date_Timespan();
$this->date_timespan->method();
```

The following functions are available:

#### $this->date_timespan->getTime()

Formats a unix timestamp so that is appears similar to this:

```php
1 Year, 10 Months, 2 Weeks, 5 Days, 10 Hours, 16 Minutes
```

The first parameter must contain a Unix timestamp. The second parameter must contain a timestamp that is greater that the first timestamp. If the second parameter empty, the current time will be used. The most common purpose for this function is to show how much time has elapsed from some point in time in the past to now. Example:

```php
$post_date = '1079621429';
$now = time();

echo $this->date_timespan->getTime($post_date, $now);
```

**Note:** The text generated by this function is found in the following language file: <kbd>app/translations/en_US/date_format.php</kbd>