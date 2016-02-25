<?php 
/*
 *Plugin Name: Ajax post query
 *Author: Uzzal Mondal
 *Description: WordPress Ajax post query plugin is an nice tool to query your posts/pages .
 *Version: 1.0.0
 *License: GPL3 http://www.gnu.org/licenses/gpl-3.0.html
 *@sience 2/24/2016
 *@pakege ajax_post_query
 */

 class Ajax_post_query{
     private static $instance;
     protected $_pluginName ;
     protected $_pluginVers ;  
     function __construct($hook){
        $this->_pluginName = $hook['pluginName'];
        $this->_pluginVers = $hook['pluginVers'];
        $this->wp_enqueue();
        $this->apq_action();
      
    }
     public static function getInstance()
      {
        if(!self::$instance)
        {
          self::$instance = new Ajax_post_query($hook = array('pluginName' =>'Ajax post query' ,'pluginVers' =>'1.0.0' ));
        }
        return self::$instance;
      }

      public function wp_enqueue(){
        $this->wp_enqueue_style();
        $this->wp_enqueue_script();
      }
      public function wp_enqueue_style(){
           wp_register_style( 'bootstrap.css', plugin_dir_url( __FILE__ ) . '/css/bootstrap.min.css', array(), '1', 'all' );
           wp_enqueue_style('bootstrap.css');
      }
      public function wp_enqueue_script(){

             wp_enqueue_script( $this->_pluginName, plugin_dir_url( __FILE__ ) . 'js/apq.js', array( 'jquery' ),$this->_pluginVers, false );
             wp_enqueue_script( 'bootstrapJquery', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ),$this->_pluginVers, false );
      }
      public function apq_action(){
      	  add_action( 'admin_menu', array( $this, 'apq_add_menu_page' ) );
      	  add_action( 'wp_ajax_ajaxProsessData', array( $this, 'getPostData' ) );
      }
      public function apq_add_menu_page(){
          add_menu_page('apq','Post Query','manage_options','apq',array($this,'menu_page_viwe'));
      }
      public function menu_page_viwe(){
      	echo "<h3>{$this->_pluginName} plugin </h3>";
      	?>
      	  <div class ='row'>
      	  <div>
           <form id='formsub' method='post' action=''>
           	<label for='post_per_page'> Post Per Page:
            <input  class='wrap' name ='ppp' id='ppp'>
           	</label><br>
            <input name="save" type="submit" class="button button-primary" value="Get Result" style=''>
           </form>
          </div>
          <div id='allpost'></div>
      	<?php
      } 

      public function getPostData(){
        $count = $_POST['count'];
        $args = array(
                      'post_type'        => 'post',  
                      'posts_per_page'   => $count,
                      'author'           => '',                                    
                    );
         $results = get_posts($args);
        

           echo '<table class="table">';
           echo '<thead>';
           echo '<tr>';
           echo '<th>Post Title</th>';
           echo '<th>Post Date</th>';        
           echo '<th>Last Modified Date</th>';        
           echo '<th>Post Comment</th>';        
           echo '<th>Comment By</th>';        
           echo '<th>View Post</th>';        
           echo '</tr>';
           echo '</thead>';
           echo '<tbody>';
           $bgCount = 0;
           $bg='';
           $max_comment=0;
           $min_comment=0;
           $max_comment_post_title ='';
          foreach ($results as $result) {
              
               if($bgCount%2==1){$bg='#eee';}else$bg='#fff';
               echo "<tr style='background:".$bg."'>";
               echo "<td>". $result->post_title ."</td>";
               echo "<td>". $result->post_date ."</td>";
               echo "<td>". $result->post_modified ."</td>";
               echo "<td>". $result->comment_count ."</td>";
               echo "<td>". $result->post_author ."</td>";
               echo "<td><a href='". $result->guid ."'>View</a></td>";
               echo "</tr>";
               $max_comment= $result->comment_count;

               if($max_comment > $min_comment ){
                   $temp = $max_comment;               
                   $min_comment = $temp;
                   $max_comment_post_title = $result->post_title;
               }else {
                   $max_comment = $min_comment ;
                  
               };
               $bgCount++;
                    
          }
          echo '</tbody>';
          echo '</table>';
          echo '<div>';
          echo '<h4> Maximum Comment Post :</h4> <p style="color:green">'. $max_comment_post_title.'</p>';
          echo '</div>';
          echo '<pre>';
          var_dump($results);
         die();
      }
 }

  $instance = Ajax_post_query::getInstance();