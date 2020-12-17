<?php


namespace ChameleonSystem\GraphQLBundle\Command;


use ChameleonSystem\GraphQLBundle\Service\SchemaCreator;
use mysql_xdevapi\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateSchemaCommand extends Command
{
    /**
     * @var SchemaCreator
     */
    private $schemaCreator;

    public function __construct(SchemaCreator $schemaCreator)
    {
        parent::__construct(null);
        $this->schemaCreator = $schemaCreator;
    }


    protected function configure()
    {
        $this
            ->setName('chameleon_system:graph_ql:create_schema')
            ->setDescription('Create a schema based on the current db configuration')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command will create a schema based on the current db config.
Whenever the config in the db is changed, this command must be used to create an updated schema.
Save the result into the configured schema file in your project.
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schema = $this->schemaCreator->createSchemaFile();
        $output->writeln($schema);
    }


}