<?php

namespace Uniqoders\Game\Console;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class GameCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('game')
            ->setDescription('New game: you vs computer')
            ->addArgument('name', InputArgument::OPTIONAL, 'what is your name?', 'Player 1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(PHP_EOL . 'Made with ♥ by Uniqoders.' . PHP_EOL . PHP_EOL);

        $player_name = $input->getArgument('name');

        // Build players data
        $players = [
            'player' => [
                'name' => $player_name,
                'stats' => [
                    'draw' => 0,
                    'victory' => 0,
                    'defeat' => 0,
                ]
            ],
            'computer' => [
                'name' => 'Computer',
                'stats' => [
                    'draw' => 0,
                    'victory' => 0,
                    'defeat' => 0,
                ]
            ]
        ];

        /**
         * types of Game
         */
        $type_game = [
            0 => 'Traditional',
            1 => 'The Big Bang Teory'
        ];

        $ask = $this->getHelper('question');

        $question = new ChoiceQuestion('Please select type of game', array_values($type_game), 1);
        $question->setErrorMessage('Type of game %s is invalid.');
        $game_selected = $ask->ask($input, $output, $question);

        $weapons = $this->get_weapons_by_game_type($game_selected);
        $rules = $this->get_rules_by_game_type($game_selected);

        /**
         * Define numbers of Rounds 
         */
        $question = new Question('Please enter the number of rounds: ');
        $question->setValidator(function ($answer) {
            if (!is_numeric($answer)) {
                throw new \RuntimeException(
                    'The answer must be an \'Integer\''
                );
            }
            return $answer;
        });
        $max_round = $ask->ask($input, $output, $question);

        $round = 1;

        $points_win = $this->calculate_point_to_win($max_round);

        do {
            // User selection
            $question = new ChoiceQuestion('Please select your weapon', array_values($weapons), 1);
            $question->setErrorMessage('Weapon %s is invalid.');

            $user_weapon = $ask->ask($input, $output, $question);
            $output->writeln('You have just selected: ' . $user_weapon);
            $user_weapon = array_search($user_weapon, $weapons);

            // Computer selection
            $computer_weapon = array_rand($weapons);
            $output->writeln('Computer has just selected: ' . $weapons[$computer_weapon]);

            // Assing points 
            if ($user_weapon === $computer_weapon) {
                $players['player']['stats']['draw']++;
                $players['computer']['stats']['draw']++;
                $output->writeln('Draw!');
            } else if (in_array($computer_weapon, $rules[$user_weapon])) {
                $players['player']['stats']['victory']++;
                $players['computer']['stats']['defeat']++;
                $output->writeln($player_name . ' win!');
            } else {
                $players['player']['stats']['defeat']++;
                $players['computer']['stats']['victory']++;
                $output->writeln('Computer win!');
            }

            // Cycle ends if there is already a winner
            if ($players['player']['stats']['victory'] === $points_win || $players['computer']['stats']['victory'] === $points_win) {
                break;
            }

            $round++;
        } while ($round <= $max_round);

        // Display stats
        $stats = $players;

        $stats = array_map(function ($player) {
            return [$player['name'], $player['stats']['victory'], $player['stats']['draw'], $player['stats']['defeat']];
        }, $stats);

        $table = new Table($output);
        $table
            ->setHeaders(['Player', 'Victory', 'Draw', 'Defeat'])
            ->setRows($stats);

        $table->render();

        return Command::SUCCESS;
    }

    /**
     * Return weapons by type of game
     * @param String $game_selected 
     */
    public function get_weapons_by_game_type($game_selected)
    {
        switch ($game_selected) {
            case 'Traditional':
                // Weapons available
                return [
                    0 => 'Scissors',
                    1 => 'Rock',
                    2 => 'Paper'
                ];

            case 'The Big Bang Teory':
                // Weapons available  TBBT
                return [
                    0 => 'Scissors',
                    1 => 'Rock',
                    2 => 'Paper',
                    3 => 'Spok',
                    4 => 'Lizard',
                ];

            default:
                throw new Exception('There are no weapons for this type of game!!!');
        }
    }

    /**
     * Return Rules by type of game
     * @param String $game_selected 
     */
    public function get_rules_by_game_type($game_selected)
    {
        switch ($game_selected) {
            case 'Traditional':
                // Weapons available
                return [
                    0 => [2],
                    1 => [0],
                    2 => [1],
                ];

            case 'The Big Bang Teory':
                // Weapons available  TBBT
                return [
                    0 => [2, 4],
                    1 => [0, 4],
                    2 => [1, 3],
                    3 => [0, 1],
                    4 => [2, 3],
                ];

            default:
                throw new Exception('There are no rules for this type of game!!!');
        }
    }

    /**
     * Return Calculate of points to win 
     * @param Number $rounds 
     */
    public function calculate_point_to_win($rounds)
    {
        if (!is_numeric($rounds)) {
            throw new \RuntimeException(
                'The rounds must be an \'Numeric\''
            );
        }

        return $rounds % 2 === 0 ? ($rounds / 2) + 1 : round($rounds / 2);
    }
}
