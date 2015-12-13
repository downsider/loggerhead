<?php
/**
 * @package loggerhead
 * @copyright Copyright © 2015 Danny Smart
 */

namespace Downsider\Loggerhead\Command;

use Downsider\Loggerhead\Exception\LogProcessingException;
use Silktide\QueueBall\Queue\AbstractQueue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Downsider\Loggerhead\Log\MessageProcessor;
use Downsider\Loggerhead\Log\LogRepository;

class ProcessLogMessage extends Command
{

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var AbstractQueue
     */
    protected $queue;

    /**
     * @var MessageProcessor
     */
    protected $procesor;

    /**
     * @var LogRepository
     */
    protected $logRepo;

    public function __construct(AbstractQueue $queue, MessageProcessor $processor, LogRepository $logRepo)
    {
        $this->queue = $queue;
        $this->procesor = $processor;
        $this->logRepo = $logRepo;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName("downsider:loggerhead:process")
            ->setDescription("Process log messages from the queue into the database");

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->output->writeln("receiving log message");
        //$queueMessage = $this->queue->receiveMessage();

        try {
            //$this->procesor->process($queueMessage->getMessage());
            $this->procesor->process(json_encode(["format" => "test", "entry" => ["timestamp" => 15263547]]));
        } catch (LogProcessingException $e) {
            $this->displayError($e);
            return;
        }

        $format = $this->procesor->getFormat();
        $this->output->writeln("<comment>Using the '{$format->getName()}' format</comment>");

        die("\nformat: " . print_r($format, true));

        $this->logRepo->setFormat($format);
        $this->logRepo->save($this->procesor->getLog());
        $this->output->writeln("<info>Log record saved</info>");
    }

    protected function displayError(LogProcessingException $e)
    {
        $this->output->writeln("<error> {$e->getMessage()} </error>");
        $comment = $e->getComment();
        if (!empty($comment)) {
            $this->output->writeln("<comment>$comment</comment>");
        }
        $data = $e->getData();
        if (!empty($data)) {
            $this->output->writeln($data);
        }
    }

} 