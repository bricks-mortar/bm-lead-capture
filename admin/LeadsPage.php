<?php
class LeadsPage {

    public function __construct() {

    }

    public function create_lead_page() {
        global $wpdb;

        $leads = $wpdb->get_results("SELECT * FROM wp_leads");

        echo '<table style="background: white; padding: 15px;"><tbody>';
        $i = 1;
        foreach($leads as $lead) {
            echo '<tr>';
            echo '<td>' . $i . '.</td>';
            echo '<td style="padding-left: 25px;">' . $lead->email . '</td>';
            echo '</tr>';
            $i++;
        }
        echo '</tbody></table>';
    }
}