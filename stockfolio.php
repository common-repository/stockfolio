<?php
/*
Plugin Name: Stock Portfolio
Description: Outputs PNL and holdnings  of your stock portfolio
Version: 0.0.1
Author: Thomas L
Plugin URI: http://dev.liajnad.se/stockfolio
Author URI: http://dev.liajnad.se
*/
/*  
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/


require_once ( dirname(__FILE__) . '/inc.swg-plugin-framework.php');

$DB_PREFIX=$wpdb->prefix;
$DB=$DB_PREFIX."z_stockfolio";
$table=$DB_PREFIX."z_stockfolio";







// Widget Support begins here

function widget_stockfolio_init() {

    // Check to see required Widget API functions are defined...
    if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
        return; // ...and if not, exit gracefully from the script.

    // This function prints the sidebar widget--the cool stuff!
    function widget_stockfolio($args) {

        // $args is an array of strings which help your widget
        // conform to the active theme: before_widget, before_title,
        // after_widget, and after_title are the array keys.
        extract($args);

        // Collect our widget's options, or define their defaults.
        $options = get_option('widget_stockfolio');
        $title = empty($options['title']) ? 'StockFolio' : $options['title'];
        //$text = empty($options['text']) ? 'Hello World!' : $options['text'];

         // It's important to use the $before_widget, $before_title,
         // $after_title and $after_widget variables in your output.
        echo $before_widget;
        echo $before_title . $title . $after_title;
	$stockstring=stockstring();
?>	<a href="/stockfolio"><img src="/wp-content/plugins/stockfolio/image.php?data=<?php echo $stockstring; ?>" width="200" /></A> <?

        #echo get_stock_quote();
        echo $after_widget;
    }

    // This is the function that outputs the form to let users edit
    // the widget's title and so on. It's an optional feature, but
    // we'll use it because we can!
    function widget_stockfolio_control() {

        // Collect our widget's options.
        $options = get_option('widget_stockfolio');

        // This is for handing the control form submission.
        if ( $_POST['stockfolio-submit'] ) {
            // Clean up control form submission options
            $newoptions['title'] = strip_tags(stripslashes($_POST['sqsidebar-title']));
            //$newoptions['text'] = strip_tags(stripslashes($_POST['mywidget-text']));


            // If original widget options do not match control form
            // submission options, update them.
            if ( $options != $newoptions ) {
              $options = $newoptions;
              update_option('widget_stockfolio', $options);
            }
        }

        // Format options as valid HTML. Hey, why not.
        $title = htmlspecialchars($options['title'], ENT_QUOTES);
        //$text = htmlspecialchars($options['text'], ENT_QUOTES);
	$title = "StockFolio";
// The HTML below is the control form for editing options.
?>
        <div>
        <label for="sqsidebar-title" style="line-height:35px;display:block;">Widget title: <input type="text" id="sqsidebar-title" name="sqsidebar-title" value="<?php echo $title; ?>" /></label>
        <input type="hidden" name="sqsidebar-submit" id="sqsidebar-submit" value="1" />
        </div>
    <?php
    // end of widget_sqsidebar_control()
    }

    // This registers the widget. About time.
    register_sidebar_widget('StockFolio Sidebar', 'widget_stockfolio');

    // This registers the (optional!) widget control form.
    register_widget_control('StockFolio Sidebar', 'widget_stockfolio_control');
}

// Delays plugin execution until Dynamic Sidebar has loaded first.
add_action('plugins_loaded', 'widget_stockfolio_init');

function stockstring()
{
global $myStockFolio,$StockFolio;

$list=$myStockFolio->g_opt['stockfolio_portfolio'];
$stocks= explode("\n", $list);

foreach ($stocks as $i => $value) {
    $stocks2[$i]=explode(",",$stocks[$i]);
}

foreach ($stocks2 as $i => $value) {
        $values=$values.trim($stocks2[$i][2])."*";
        $name=$name.$stocks2[$i][0]."*";
}

$stringa=trim($values,"*")."&label=".trim($name,"*");
return $stringa;

}

// [bartag foo="foo-value"]
function bartag_func($atts) {

	extract(shortcode_atts(array(
		'showpercent' => 'false',
		'pnl' => 'no',
	), $atts));
$stockstring=stockstring();
	return "<img src=\"/wp-content/plugins/stockfolio/image.php?data={$stockstring}&show_percent={$showpercent}\" width=400 /><br>showpercent = {$showpercent}<br> PNL = {$pnl}";
}
add_shortcode('stockfolio', 'bartag_func');

class StockFolio extends StockFolio_SWGPluginFramework {

 function ApplyStockFolio() {


}


/**
       * Convert option prior to save ("COPTSave").
         * !!!! This function is used by the framework class !!!!
         */
        function COPTSave($optname) {
                switch ($optname) {
                        case 'mamo_excludedpaths':                            return $this->LinebreakToWhitespace($_POST[$optname]);                        default:                                return $_POST[$optname];
                } // switch
        }


        /**
         * Convert option before HTML output ("COPTHTML").
         * *NOT* used by the framework class
         */
        function COPTHTML($optname) {
                $optval = $this->g_opt[$optname];
                switch ($optname) {
                        case 'mamo_excludedpaths':
                                return $this->WhitespaceToLinebreak($optval);
                        case 'mamo_pagetitle':
                                return htmlspecialchars(stripslashes($optval));
                        case 'mamo_pagemsg':
                                return htmlspecialchars(stripslashes($optval));
                        default:
                                return $optval;
                } // switch
        }


function PluginOptionsPage() {
 $this->AddContentMain(__('Activate/Deactivate ',$this->g_info['ShortName']), "
                        <table border='0'><tr>
				<h2>Function not implemented.</h2><br>
                                <td width='130'>
                                        <p style='font-weight: bold; line-height: 2em;'>
                                                <input id='radioa1' type='radio' name='mamo_activate' value='on' " . ($this->COPTHTML('mamo_activate')=='on'?'checked="checked"':'') . " />
                                                <label for='radioa1'>".__('Activated',$this->g_info['ShortName'])."</label>
                                                <br />
                                                <input id='radioa2' type='radio' name='mamo_activate' value='off' " . ($this->COPTHTML('mamo_activate')!='on'?'checked="checked"':'') . " />
                                                <label for='radioa2'>".__('Deactivated',$this->g_info['ShortName'])."</label>
                                        </p>
                                </td>
                        </tr></table>
                        ");

               $this->AddContentMain(__('Portfolio',$this->g_info['ShortName']), "
                        <table width='100%' cellspacing='2' cellpadding='5' class='editform'> 
                        <tr valign='center'> 
  <tr valign='top'> 
                                <th align=left width='150px' scope='row'><label for='stockfolio_portfolio'>".__('',$this->g_info['ShortName'])."</label></th> 
                                <td width='100%'><textarea style='font-size: 90%; width:95%;' name='stockfolio_portfolio' id='stockfolio_portfolio' rows='15' >" . $this->COPTHTML('stockfolio_portfolio') . "</textarea>
                                <p class='info'>".__('Use the folling format:<br>STOCK.ST,ANTAL,TOTINK One Stock per row.',$this->g_info['ShortName'])."</p>

                        </tr>
</table>
                        ");





                // Sidebar, we can also add individual items...
                $this->PrepareStandardSidebar();

                $this->GetGeneratedOptionsPage();



}

}

if( !isset($myStockFolio)  ) {
        // Create a new instance of your plugin that utilizes the WordpressPluginFramework and initialize the instance.
        $myStockFolio = new StockFolio();

        $myStockFolio->Initialize(
                // 1. We define the plugin information now and do not use get_plugin_data() due to performance.
                array(
                        # Plugin name
                                'Name' =>                       'StockFolio',
                        # Author of the plugin
                                'Author' =>             'Thomas Lindholm',
                        # Authot URI
                                'AuthorURI' =>          'http://dev.liajnad.se/',
                        # Plugin URI
                                'PluginURI' =>          'http://dev.liajnad.se/',
                        # Support URI: E.g. WP or plugin forum, wordpress.org tags, etc.
                                'SupportURI' =>         'http://wordpress.org/tags/maintenance-mode',
                        # Name of the options for the options database table
                                'OptionName' =>         'StockFolio',
                        # Old option names to delete from the options table; newest last please
                                'DeleteOldOpt' =>       array('StockFolio1', 'StockFolio2'),
                        # Plugin version
                                'Version' =>            '1.0',
                        # First plugin version of which we do not reset the plugin options to default;
                        # Normally we reset the plugin's options after an update; but if we for example
                        # update the plugin from version 2.3 to 2.4 und did only do minor changes and
                        # not any option modifications, we should enter '2.3' here. In this example
                        # options are being reset to default only if the old plugin version was < 2.3.
                                'UseOldOpt' =>          '2.3',
                        # Copyright year(s)
                                'CopyrightYear' =>      '2010-2010',
                        # Minimum WordPress version
                                'MinWP' =>                      '2.9',
                        # Do not change; full path and filename of the plugin
                                'PluginFile' =>         __FILE__,
                        # Used for language file, nonce field security, etc.
                                'ShortName' =>          'StockFolio',
                        ),

                // 2. We define the plugin option names and the initial options
                array(
                        'mamo_activate' =>                      'on',
                        'mamo_excludedpaths' =>         '',
                        'mamo_backtime' =>                      '60',
                        'mamo_pagetitle' =>             'StockFolio',
                        'stockfolio_portfolio' =>                       'SHB-A.ST,200,30000' . "\n" . 'MEDA-B.ST,100,6350' . "\n",
                        'mamo_noaccesstobackend' => '',
                        'mamo_use_503_php' => '',
                ));

}
        $myStockFolio->ApplyStockFolio();

			
?>
