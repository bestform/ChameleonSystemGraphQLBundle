<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\GraphQLBundle\Command;

use ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesCacheWarmer;
use ChameleonSystem\GraphQLBundle\Generator\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GenerateSchemaCommand extends Command
{

    /**
     * @var Schema
     */
    private $generator;

    public function __construct(Schema $generator)
    {
        parent::__construct('chameleon_system:graphql:generate_schema');

        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Generates a schema for a database table')
            ->addArgument('table')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generating schema...');
        $table = $input->getArgument('table');
        $result = $this->generator->schemaForTable($table);

        $output->writeln($result->toSchemaFile());
        $output->writeln($result->toResolver());

        return 0;
    }
}
