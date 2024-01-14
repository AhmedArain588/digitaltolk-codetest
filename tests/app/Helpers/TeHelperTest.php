<?php
namespace tests\app\Helpers;

use Carbon\Carbon;
use DTApi\Helpers\TeHelper;
use tests\TestCase;

class TeHelperTest extends TestCase
{
    public function testWillExpireAt()
    {
        // Assuming current date and time
        $now = Carbon::now();

        // Set up the expected result based on your logic
        $expectedResult = $now->copy()->addMinutes(90)->format('Y-m-d H:i:s');

        // Call the function with some sample inputs
        $result = TeHelper::willExpireAt($now->copy()->addMinutes(90), $now);

        // Assert that the result matches the expected result
        $this->assertEquals($expectedResult, $result);
    }
}

// php artisan test
// with the upper command test will run