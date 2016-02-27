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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

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
          add_menu_page('apq','Post Query','manage_options','apq',array($this,'menu_page_viwe'),'dashicons-welcome-view-site');
      }
      public function menu_page_viwe(){
      	echo "<h3>{$this->_pluginName} plugin </h3>";
      	?>
      	  <div class ='row'>
      	  <div>
           <form id='formsub' method='post' action=''>
           	<label for='post_per_page'>Number of Post:
            <input  name ='ppp' id='ppp' type='number' style='width:100px'>
           	</label>
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
        
           echo '<div class="row">';

           echo '<div class="col-sm-5">'; 
           echo '<table class="table">';
           echo '<thead>';
           echo '<tr>';
           echo '<th>Post Title</th>';      
           echo '<th>Last Modified Date</th>';        
           echo '<th>Post Comment</th>';                
           echo '<th>View Post</th>';        
           echo '</tr>';
           echo '</thead>';
           echo '<tbody>';
           $bgCount = 0;
           $bg='';
           $max_comment=0;
           $min_comment=0;
           $max_comment_post_title ='';
           $color='';
          foreach ($results as $result) {
              
               if($bgCount%2==1){$bg='#484848';$color='#fff';}else{$bg='#282828';$color='#fff';}
               echo "<tr style='background:".$bg.";color:".$color."'>";
               echo "<td>". $result->post_title ."</td>";
               echo "<td>". $result->post_modified ."</td>";
               echo "<td>". $result->comment_count ."</td>";
               echo "<td><a href='". $result->guid ."' target='_blank'>View</a></td>";
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
          echo '<h4> Maximum Comment Post :</h4>
                <p style="background:'.$bg.';color:'.$color.'; padding:10px;text-align:center">
                '. $max_comment_post_title.'
              </p>';
          echo '</div>';
          echo '</div>';

           echo '<div class="col-sm-6">'; 
           echo '<table class="table">';
           echo '<thead>';
           echo '<tr>';
           echo '<th>Comment Author</th>';         
           echo '<th>Comment Author email</th>';                
           echo '<th>View Post</th>';        
           echo '</tr>';
           echo '</thead>';
           echo '<tbody>';
           $args = array(  
                'post_id' => 0,
                'count' => false,   
                'date_query' => null,
                );

             // The Query
             $comments_query = new WP_Comment_Query;
             $comments = $comments_query->query( $args );
           $bgCount = 0;
           $bg='';
           $max_comment_author ='';
           $comment_author ='';
           $comment_author_email ='';
           $color='';
          foreach ( $comments as $comment ) {
              
               if($bgCount%2==1){$bg='#484848';$color='#fff';}else{$bg='#282828';$color='#fff';}
               echo "<tr style='background:".$bg.";color:".$color."'>";
               echo "<td>". $comment->comment_author ."</td>";
               echo "<td>". $comment->comment_author_email."</td>";
               echo "<td>". $comment->post_name."</td>";
               echo "</tr>";
               $comment_author .= $comment->comment_author.'/';
               if($comment->comment_author === $max_comment_author){
                  $max_comment_author = $comment->comment_author;
               }
               $bgCount++;
                    
          }
          echo '</tbody>';
          echo '</table>';
          echo '<div>';
          $authors = preg_split("/[\/]+/", $comment_author);        
          $author = '';
          $count = 1; 
          $totalElement = 0;
          $shorting_author = '';

          /*
          SELECT COUNT(  `user_id` ) AS totalcomment,  `comment_author` ,  `comment_author_email` 
          FROM  `wp_comments` 
          WHERE 1 
          GROUP BY  `user_id` 
          ORDER BY totalcomment DESC 
          LIMIT 0 , 30
          */

          foreach ( $authors as  $author) {
               
                $totalElement++;
            
          }
          for( $i=0; $i<$totalElement; $i++){

              for($j= $i+1;$j<$totalElement;$j++){
                    if( $authors[$i] === $authors[$j] ){
                       $author = $authors[$i]; 
                       $count++; 
                    }else if(($totalElement-1)== $j){
                       $shorting_author .= $author.'*'.$count.'/';                
                       $author='';
                       $count=1;
                    }
                
              }
          }
         // echo $shorting_author;
          $authors = preg_split("/[\/]+/", $shorting_author);
          $authors_match ='';
          $totalElement = 0;
          foreach ( $authors as  $author) {

                 $returnValue = preg_match('/^[a-zA-Z][^0-9]+[0-9]$/', $author, $matches);
                 $authors_match .= $matches[$totalElement].'  /'; 
                 $totalElement++;
            
          }
          echo $authors_match;
          echo '<h4> Maximum Comment Author :</h4>
                <p style="background:'.$bg.';color:'.$color.'; padding:10px;text-align:center">
                '. $author.' 
              </p>';
          echo '</div>';
          echo '</div>';
  
          echo '</div>';
        
         die();
      }

      
   }

  $instance = Ajax_post_query::getInstance();