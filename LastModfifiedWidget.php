<?php
/*
Plugin Name: Last modified items widget
Plugin URI: http://online-source.net/2010/11/17/last-modified-posts-widget/
Description: Show the last modified items in a widget with several options.
Author: Laurens ten Ham (MrXHellboy)
Version: 1.1.5
Author URI: http://online-source.net
*/

class Last_Modified extends WP_Widget 
{

	function Last_Modified() 
    {
		$widget_options = array(
                            'classname'         => 'last_modified_widget', 
                            'description'       => 'Last modified items'
                            );
                            
		$this->WP_Widget('last-modifed', 'Last modified items', $widget_options);
	}
	
	function GetLastModified($instance)
	{
		global $wpdb;
            # Create variables from the array indexes
            # title, amount, post_type, text_between, date_format
            extract($instance);
            
            # Determine query
            $post_type = ($post_type == 'all') ? "post_type LIKE '%'" : "post_type = '{$post_type}'";
            
            $LastModified = $wpdb->get_results("SELECT ID, post_title, DATE_FORMAT(post_modified, '$date_format') AS date
                                                 FROM {$wpdb->posts}
                                                 WHERE post_status = 'publish'
                                                 AND {$post_type}
                                                 AND post_date <> post_modified
                                                 ORDER BY post_modified
                                                 DESC LIMIT 0 , ".$amount
                                                    );

		$LM_list =  '<ul>';
		foreach ($LastModified as $list)
		{
			$LM_list .= '<li><a href="'. get_permalink($list->ID) .'" title="'.$list->post_title.'">'.$list->post_title.'</a> '.$text_between .' '. $list->date.'</li>';
		}
		$LM_list .=  '</ul>';
		
		return $LM_list;
	}
    
    function GetThePostTypes($preselect)
    {
        # Empty string
        $options = '';
        
        # Get post types
        $CustomPostTypes = get_post_types(
                                            array(
                                                    'exclude_from_search' => false
                                                 ), 
                                            'names'
                                         );
        
        # Prepend all to the array                                   
        array_unshift($CustomPostTypes, 'all');
            
            # Loop through the post types array
            foreach ($CustomPostTypes as $type)
            {
                $options .= ($type == $preselect) ? '<option value="'.$type.'" selected="selected">'.$type.'</option>' : '<option value="'.$type.'">'.$type.'</option>';
            }
            
        return $options;
    }

	function widget($args, $instance) 
	{
		extract($args);

		$widget  = $before_widget;
		$widget .= $before_title . strip_tags(apply_filters('widget_title', $instance['title'])) . $after_title;
		$widget .= $this->GetLastModified($instance);
		$widget .= $after_widget;
            echo $widget;
	}

	function update($new_instance, $old_instance) 
	{
		$instance                     = $old_instance;
		$instance['title']            = strip_tags($new_instance['title']);
		$instance['amount']           = strip_tags($new_instance['amount']);
        $instance['post_type']        = strip_tags($new_instance['post_type']);
        $instance['text_between']     = strip_tags($new_instance['text_between']);
        $instance['date_format']      = strip_tags($new_instance['date_format']);
		  return $instance;
	}

	function form($instance) 
	{
		$instance             = wp_parse_args((array)$instance, array('title' => 'Last modified items', 'amount' => 5, 'post_type' => 'post', 'text_between' => 'at', 'date_format' => '%m-%d-%Y'));
		$title                = strip_tags($instance['title']);
		$amount               = strip_tags($instance['amount']);
        $PostType             = strip_tags($instance['post_type']);
        $text_between         = strip_tags($instance['text_between']);
        $date_format          = strip_tags($instance['date_format']);
?>
			<p>
                <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
            </p>
            
			<p>
                <label for="<?php echo $this->get_field_id('amount'); ?>">Amount:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('amount'); ?>" name="<?php echo $this->get_field_name('amount'); ?>" type="text" value="<?php echo attribute_escape($amount); ?>" />
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('text_between'); ?>">Text between title and date:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('text_between'); ?>" name="<?php echo $this->get_field_name('text_between'); ?>" type="text" value="<?php echo attribute_escape($text_between); ?>" />
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('date_format'); ?>">Date format:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('date_format'); ?>" name="<?php echo $this->get_field_name('date_format'); ?>" type="text" value="<?php echo attribute_escape($date_format); ?>" />
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('post_type'); ?>">Post type:</label>
                <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
                                                                                        <?php echo $this->GetThePostTypes($PostType); ?>
                </select>
            </p>
<?php
	}
}

add_action('widgets_init', 'RegisterLastModifiedWidget');

function RegisterLastModifiedWidget() {
	register_widget('Last_Modified');
}
?>