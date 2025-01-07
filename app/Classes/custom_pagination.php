<?php
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;

class custom_pagination
{
	public function paginate_ajax($sorgu_bilesenleri,$sorgu_degiskenleri,$sayfa_goster=0)
	{
		
		if($sayfa_goster==0)
			$sayfa_goster=11;

		$sorgu 		= $sorgu_bilesenleri;
		$eleman_1 	= stripos($sorgu, "SELECT");
		$eleman_2 	= stripos($sorgu, "FROM");
		
		if($eleman_1=== false && $eleman_2=== false)
		{
			return;
		}

		$parca=substr($sorgu,$eleman_1+strlen("SELECT"),$eleman_2- ($eleman_1+strlen("SELECT")) );
		//echo $parca;
		$sorgu=str_replace($parca," count(*) as satir_sayisi ",$sorgu); 

		$toplam_satir=DB::select($sorgu,$sorgu_degiskenleri);
		$toplam_satir_sayisi=$toplam_satir[0]->satir_sayisi;
		$toplam_sayfa = ceil($toplam_satir_sayisi / $sayfa_goster);
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$actual_link=str_replace("=","",$actual_link); 
		//dd($actual_link);
		setcookie("total_page_".$actual_link, $toplam_sayfa);
		$sayfa = isset($_GET['page']) ? (int) $_GET['page'] : 1;
 		if(!isset($_GET['page']))
 			$sayfa=1;
		if($sayfa < 1) $sayfa = 1; 
		if($sayfa > $toplam_sayfa) $sayfa = $toplam_sayfa; 
		 
		$limit = ($sayfa - 1) * $sayfa_goster;
		if($limit<0)
			$limit=0;
		$yeni_sorgu = $sorgu_bilesenleri." LIMIT " . $limit . ', ' . $sayfa_goster;
		//dd($yeni_sorgu);
		Log::info(' yeni= '.$yeni_sorgu."  ".date("Y-m-d H:i:s"));

		$calistir=DB::select($yeni_sorgu,$sorgu_degiskenleri);

 		return $calistir;

	}

	public function create_links()
	{
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$actual_part = "$_SERVER[REQUEST_URI]_paginate";
		echo "
		<script>
		function paginate_ajax(clicked_page)
		{
			document.getElementById('ajax_pagination').innerHTML='YUKLENİYOR';
			
			$.ajax
			({
				type:'get',
				url:'$actual_part?page='+clicked_page,
				data:$().serialize(),
				success:function(data)
				{
					$('#ajax_pagination').html(data.ajax_pagination);
				}
			});
		}
		</script>
		";
		$sayfa = isset($_GET['page']) ? (int) $_GET['page'] : 1;
		if (isset($_COOKIE["total_page_".$actual_link]))
		{
			$toplam_sayfa=$_COOKIE["total_page_".$actual_link];
			echo'<div  class="pagination center">';
			for($s = 1; $s <= $toplam_sayfa; $s++) 
			{
			   if($sayfa == $s) 
			   { // eğer bulunduğumuz sayfa ise link yapma.
			      echo  '<a onclick="paginate_ajax('.$s.');" href="#" class="active">'.$s.'</a> '; 
			   } 
			   else 
			   {
			      echo '<a onclick="paginate_ajax('.$s.');" href="#">'.$s.'</a> ';
			   }
			}
		}
	}
}
	
?>

    
    