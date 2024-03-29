<?php

class ProjectTemplateDebugger implements sfTemplateDebuggerInterface
{
  protected $messages = array();

  public function log($message)
  {
    $this->messages[] = $message;
  }

  public function hasMessage($regex)
  {
    foreach ($this->messages as $message)
    {
      if (preg_match('#'.preg_quote($regex, '#').'#', $message))
      {
        return true;
      }
    }

    return false;
  }

  public function getMessages()
  {
    return $this->messages;
  }
}
