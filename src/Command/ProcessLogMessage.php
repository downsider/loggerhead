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
    protected $processor;

    /**
     * @var LogRepository
     */
    protected $logRepo;

    public function __construct(AbstractQueue $queue, MessageProcessor $processor, LogRepository $logRepo)
    {
        $this->queue = $queue;
        $this->processor = $processor;
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
        $this->output->writeln("Receiving log message");
        $queueMessage = $this->queue->receiveMessage();

        try {
            $this->processor->process($queueMessage->getMessage());
            $format = $this->processor->getFormat();
            $this->output->writeln("<comment>Using the '{$format->getName()}' format</comment>");
            $this->logRepo->setFormat($format);
            $this->logRepo->save($this->processor->getLog());
            $this->output->writeln("<info>Log record saved</info>");
            $this->queue->completeMessage($queueMessage);
        } catch (LogProcessingException $e) {
            $this->displayError($e);
            $this->queue->returnMessage($queueMessage);
            return;
        }
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