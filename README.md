# FreeBusyCalculator
## Usage
```php
use FreeBusyCalculator\FreeBusyCalculator;

$calc = new FreeBusyCalculator;
$calc->addBusyRanges([
    ['2019-01-01T00:00:00+0000', '2019-01-31T23:59:59+0000'],
    ['2019-03-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
]);
$freetimeRanges = $calc->getFreetime([
    '2019-01-01T00:00:00+0000', '2019-03-31T23:59:59+0000'
]);

echo "Freetimes\n";
foreach ($freetimeRanges as $freetimeRange) {
    echo "start: {$freetimeRange->start}, end: {$freetimeRange->end}\n";
}
```
