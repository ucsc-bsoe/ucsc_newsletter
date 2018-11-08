<?php

use Drupal\ucsc_newsletter\Controller\UCSCNewsletterContentController;
use Drupal\Core\Render\Markup;
use Drupal\node\Entity\Node;

function ucsc_newsletter_send() {

  // Get information from ucsc_newsletter settings configuration to use here.
  $config = \Drupal::config('ucsc_newsletter.settings');

  $last_sent_date = $config->get("last_sent_date");
  $weekday = $config->get("weekday");
  $hour = $config->get("hour");

  $current_weekday = date("w");
  $current_hour = date("H");

  // Get the newsletter content.
  $testing_controller = new UCSCNewsletterContentController();
  $newsletter_content = $testing_controller->make_newsletter();

  // Check the current configuration and if it's the right day and time, send
  // the email.
  if ($hour <= $current_hour
    and $current_weekday == $weekday
    and abs($last_sent_date - time()) > 86400) {

  //if (1 === 1) {

    // Settings to send the mail to ucsc_newsletter_mail in ucsc_newsletter.module.
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'ucsc_newsletter';
    $key = 'newsletter_weekly';
    $to = \Drupal::config('ucsc_newsletter.settings')->get('to_address');
    $params['message'] = Markup::create($newsletter_content);
    $from = \Drupal::config('ucsc_newsletter.settings')->get('from_address');
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);

    // Save new last_sent to ucsc_newsletter settings configuration.
    \Drupal::service('config.factory')->getEditable('ucsc_newsletter.settings')
      ->set('last_sent_date', strtotime('now'))
      ->save();

    // Create a new node for the newsletter.
    $node = Node::create(['type' => 'ucsc_newsletter']);
    $node->set('title', 'Newsletter for ' . date('m/d/Y'));

    $body = [
      'value' => $newsletter_content,
      'format' => 'full_html',
     ];
    $node->set('body', $body);
    //$node->set('uid', <uid>);
    $node->status = 1;
    //$node->enforceIsNew();
    $node->save();
    drupal_set_message( "Node with nid " . $node->id() . " saved!\n");

  }

  /*if (1 === 1) {

    // Settings to send the mail to ucsc_newsletter_mail in ucsc_newsletter.module.
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'ucsc_newsletter';
    $key = 'newsletter_weekly';
    $to = 'kbradham@ucsc.edu';
    $params['message'] = Markup::create($newsletter_content);
    $from = 'kbradham@ucsc.edu';
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);
  } */  
}
