<?php

namespace Uniqoders\Tests\Integration\Console;

use Exception;
use Symfony\Component\Console\Tester\CommandTester;
use Uniqoders\Game\Console\GameCommand;
use Uniqoders\Tests\Integration\IntegrationTestCase;

class GameCommandTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->application->add(new GameCommand());
    }

    public function test_game_command(): void
    {
        $this->expectException(Exception::class);

        /**
         *******************
         * TODO Make tests *
         *******************
         */
        $command = $this->application->find('game');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        $this->assertTrue(true);
    }


    /**
     * Test exception in return of weapons
     */
    public function test_exception_in_get_weapons_by_game_type()
    {
        $this->expectException(Exception::class);

        $commandTester = new GameCommand();

        $result = $commandTester->get_weapons_by_game_type('Other');
    }

    /**
     * Test exception in return of succesfull array
     */
    public function test_get_weapons_by_game_type()
    {
        $commandTester = new GameCommand();

        $result = $commandTester->get_weapons_by_game_type('Traditional');

        $this->assertIsArray(
            $result,
            "assert variable is array"
        );
    }

    /**
     * Test exception in return of rules
     */
    public function test_exception_in_get_rules_by_game_type()
    {
        $this->expectException(Exception::class);

        $commandTester = new GameCommand();

        $result = $commandTester->get_rules_by_game_type('Other');
    }

    /**
     * Test exception in return of succesfull array rules
     */
    public function test_get_rules_by_game_type()
    {
        $commandTester = new GameCommand();

        $result = $commandTester->get_rules_by_game_type('Traditional');

        $this->assertIsArray(
            $result,
            "assert variable is array"
        );
    }

    /**
     * Test exception in calculate point to win
     */
    public function test_exception_calculate_point_to_win()
    {
        $this->expectException(Exception::class);

        $commandTester = new GameCommand();

        $result = $commandTester->calculate_point_to_win('qwe');
    }

    /**
     * Test calculate point to win with even number
     */
    public function test_calculate_point_to_win_with_even_number()
    {
        $commandTester = new GameCommand();

        $result = $commandTester->calculate_point_to_win(4);

        $this->assertEquals(3,$result);
    }

    /**
     * Test calculate point to win with odd number
     */
    public function test_calculate_point_to_win_with_odd_number()
    {
        $commandTester = new GameCommand();

        $result = $commandTester->calculate_point_to_win(5);

        $this->assertEquals(3,$result);
    }
}
