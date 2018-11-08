<?php

namespace Drupal\ucsc_newsletter\Controller;
use Drupal\Core\Controller\ControllerBase;

/**
 * A testing controller.
 */
class UCSCNewsletterContentController extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function make_newsletter() {

    $ucsc_newsletter_controller = new UCSCNewsletterContentController();

    $header = $ucsc_newsletter_controller->message_header();

    $announcements = $ucsc_newsletter_controller->announcements();
    $announcements_toc = $announcements[0];
    $announcements_list = $announcements[1];
    $announcements_new = $announcements[2];

    $events = $ucsc_newsletter_controller->events();
    $events_toc = $events[0];
    $events_list = $events[1];

    $jobs = $ucsc_newsletter_controller->jobs();
    $jobs_toc = $jobs[0];
    $jobs_list = $jobs[1];

    $total_message = "<div class='email-body'>" .
      $header .
      "<div class='newsletter-item'>" .
      $announcements_new . $announcements_toc . $events_toc . $jobs_toc .
      "</div>" .
     $announcements_list . $events_list . $jobs_list . "</div>";

    return $total_message;
  }

  function message_header() {
    $header = "<h2>BSOE Undergraduate Newsletter</h2>";
    return $header;
  }

  function announcements() {
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'newsletter_announcement')
      ->condition("status", 1);
    $nids = $query->execute();
    $nodes = $node_storage->loadMultiple($nids);
    if (count($nids) != 0) {
      $toc = "";
      $message = "";
      $toc_new = "";
      foreach ($nodes as $node) {
	$end_date = strtotime($node->get('field_end_date')->value);
	$start_date = strtotime($node->get('field_start_date')->value);
        $current_date = strtotime(date("Y-m-d"));
	if (($end_date + 86400) > $current_date && ($start_date - 86400) < $current_date) {
          $message = $message .
          "<h3><a name='announce-" . $node->get('nid')->value . "' href='https://undergrad.soe.ucsc.edu" . $node->toUrl('canonical')
          ->toString() . "'>"
          . $node->get('title')->value . "</a></h3>" .
          $node->get('body')->value  . "<hr>";
          $toc =  $toc . "<a href='#announce-" . $node->get('nid')->value . "'>" . $node->get('title')->value . "</a><br>";
          $announcement_start_date = strtotime($node->get('field_start_date')->value);
          $one_week_ago = strtotime('-1 week');
          if ($announcement_start_date - $one_week_ago > 0) {
            $toc_new = $toc_new . "<a href='#announce-" . $node->get('nid')->value . "'>" . $node->get('title')->value . "</a><br>";
	  } 
	}
      }
      if ($toc_new != "") {
	$toc_new = "<h4><a href='#announcements'>New Announcements</a></h4>" . $toc_new;
      }
      if ($toc != "") {
        $toc = "<div><h4><a href='#announcements'>Announcements</a></h4>" . $toc . "</div>";
      }  
      if ($message != "") {
        $message = "<div class='newsletter-item'><a name='announcements'></a><h2>Announcements</h2>" . $message . "</div>";
      }
    }
    return [$toc, $message, $toc_new];
  }

  function events() {
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'newsletter_event')
      ->condition("status", 1);
    $nids = $query->execute();
    $nodes = $node_storage->loadMultiple($nids);
    if (count($nids) != 0) {
      $toc = "";
      $message = "";
      foreach ($nodes as $node) {
	$end_date = strtotime($node->get('field_end_date')->value);
	$start_date = strtotime($node->get('field_start_date')->value);
        $current_date = strtotime(date("Y-m-d"));
	if (($end_date + 86400) > $current_date && ($start_date - 86400) < $current_date) {
	$message = $message .
          "<h3><a name='event-" . $node->get('nid')->value . "' href='https://undergrad.soe.ucsc.edu" . $node->toUrl('canonical')
          ->toString() . "'>"
          . $node->get('title')->value . "</a></h3>" .
          "<strong>Event Date and Time:</strong> " . $time . "<br>" .
          "<strong>Event Location:</strong> " . $node->get('field_event_location')->value . "<br>" .
          $node->get('body')->value  . "<hr>";
        $toc = $toc . "<a href='#event-" . $node->get('nid')->value . "'>" . $node->get('title')->value . "</a><br>";
        }
      }
      if ($toc != "") {
        $toc = "<div><h4><a href='#events'>Events</a></h4>" . $toc . "</div>";
      }  
      if ($message != "") {
        $message = "<div class='newsletter-item'><a name='events'></a><h2>Events</h2>" . $message . "</div>";
      }
    }
    return [$toc, $message];
  }

  function jobs() {
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'newsletter_job')
      ->condition("status", 1);
    $nids = $query->execute();
    $nodes = $node_storage->loadMultiple($nids);
    if (count($nids) != 0) {
      $toc = "";
      $message = "";
      foreach ($nodes as $node) {
	$end_date = strtotime($node->get('field_end_date')->value);
	$start_date = strtotime($node->get('field_start_date')->value);
        $current_date = strtotime(date("Y-m-d"));
	if (($end_date + 86400) > $current_date && ($start_date - 86400) < $current_date) {
	$message = $message .
          "<h3><a name='job-" . $node->get('nid')->value . "' href='https://undergrad.soe.ucsc.edu" . $node->toUrl('canonical')
          ->toString() . "'>"
          . $node->get('title')->value . "</a></h3>" .
          $node->get('body')->value . "<hr>";
        $toc = $toc . "<a href='#job-" . $node->get('nid')->value . "'>" . $node->get('title')->value . "</a><br>";
	}
      }
      if ($toc != "") {
        $toc = "<div><h4><a href='#jobs'>Jobs</a></h4>" . $toc . "</div>";
      }  
      if ($message != "") {
        $message = "<div class='newsletter-item'><a name='jobs'></a><h2>Jobs</h2>" . $message . "</div>";
      }
    return [$toc, $message];
    }
  }
}
