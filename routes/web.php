<?php
use App\Http\Controllers\Api;
use App\Http\Controllers\BayiControllers;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Bayi;
use App\Http\Middleware\Yukleyici;
use App\Http\Middleware\Rapor;


Route::get('/', function () {
    return view('loginPage/Login');
});
Route::get('/giris', function () {
    return view('loginPage/Login');
});

Route::get('bayinotest','testbayino@testBayiNo');

Route::any('login-giris','LoginController@giris');
Route::any('login-Signup','LoginController@kaydol');
Route::any('cikis','LoginController@cikis');
Route::any('api.pdf','Duyurular@pdf');

Route::any('Db/Yedek/YeniYedekOlustur/Insert','YedekleCron@Yedekle');
#region Robot Raporları

Route::any('rapor-giris','RaporControllers\RaporGiris@index');
Route::any('rapor-giris-login','RaporControllers\RaporGiris@giris');
Route::any('rapor-bekleyen','RaporControllers\RaporBekleyen@index')->middleware('Rapor');
Route::any('ajax/RaporHesapHareketleri','RobotListesi@HesapHareketleri')->middleware('Rapor');
Route::any('ajax/RaporHesapHareketleri/temizle','RobotListesi@HesapHareketleriTemizle')->middleware('Rapor');
Route::any('rapor-cikis','RaporControllers\RaporGiris@cikis');
#endregion

#region Yukleyici
Route::any('yukleyici-sonuc','YukleyiciControllers\YukleyiciBekleyen@sonuc')->middleware('Yukleyici');;
Route::any('yukleyici-bekleyen','YukleyiciControllers\YukleyiciBekleyen@index')->middleware('Yukleyici');
Route::any('yukleyici-bekleyen-sayisi','YukleyiciControllers\YukleyiciBekleyen@bekleyenSayisi')->middleware('Yukleyici');
Route::any('yukleyici-giris','YukleyiciControllers\YukleyiciGiris@index');
Route::any('yukleyici-giris-login','YukleyiciControllers\YukleyiciGiris@giris');
Route::any('yukleyici-raporlar','YukleyiciControllers\YukleyiciRaporlar@RaporData');


Route::any('ajax/YukleyiciHesapHareketleri','RobotListesi@HesapHareketleri')->middleware('Yukleyici');
Route::any('ajax/YukleyiciHesapHareketleri/temizle','RobotListesi@HesapHareketleriTemizle')->middleware('Yukleyici');

Route::any('yukleyici-cikis','YukleyiciControllers\YukleyiciGiris@cikis');
#endregion

#region Bayi
Route::any('bayi-anasayfa-duyurular','BayiControllers\BayiDuyurular@index')->middleware('Bayi');


#region yuklemetakip
Route::any('bayi-kontor-yuklemetakip','BayiControllers\BayiKontorYuklemeTakip@GetData')->middleware('Bayi');
Route::any('bayi-kontor-yuklemetakip/temizle','BayiControllers\BayiKontorYuklemeTakip@Temizle')->middleware('Bayi');
#endregion
#region faturaTakip
Route::any('bayi-fatura-faturatakip','BayiControllers\BayiFaturaTakip@GetData')->middleware('Bayi');
Route::any('bayi-fatura-faturatakip/temizle','BayiControllers\BayiFaturaTakip@Temizle')->middleware('Bayi');
#endregion
#region Ayarlar
Route::any('bayi-ayarlar-bayiayarlari','BayiControllers\BayiAyarlar@index')->middleware('Bayi');
Route::any('ajax/kullaniciAyar/Update','BayiControllers\BayiAyarlar@KullaniciGuncelle')->middleware('Bayi');
#endregion

#region PaketListesi
Route::any('bayi-kontor-paketlistesi/temizle','BayiControllers\BayiKontorPaketListesi@Temizle')->middleware('Bayi');
Route::any('bayi-kontor-paketlistesi','BayiControllers\BayiKontorPaketListesi@PaketListesi')->middleware('Bayi');
#endregion

#region HesapHareketleri
Route::any('bayi-odemehareketleri-hesaphareketleri','BayiControllers\BayiHesapHareketleri@index')->middleware('Bayi');
Route::any('bayi-odemehareketleri-hesaphareketleri/temizle','BayiControllers\BayiHesapHareketleri@temizle')->middleware('Bayi');
Route::any('bayi-odemehareketleri-banka','BayiControllers\BayiHesapHareketleri@bankalar')->middleware('Bayi');
#endregion

#endregion
#region admin
#region duyurular
Route::any('anasayfa-duyurular','Duyurular@index')->middleware('Admin');
Route::any('robotIndir','Duyurular@indir')->middleware('Admin');
#endregion
#region robotListesi
Route::any('robotlistesi-robotlar/temizle','RobotListesi@Temizle')->middleware('Admin');
Route::any('robotlistesi-robotlar','RobotListesi@TumRobotlar')->middleware('Admin');//
Route::any('ajax/RobotHesap/UpdateSw','RobotListesi@UpdateSw')->middleware('Admin');
Route::any('ajax/RobotHesap/UpdateSureSinir','RobotListesi@UpdateSureSinir')->middleware('Admin');
Route::any('ajax/robotOzellikleri','RobotListesi@RobotOzellikleri')->middleware('Admin');
Route::any('ajax/YeniRobot','RobotListesi@YeniRobot')->middleware('Admin');
Route::any('ajax/RobotGuncelle','RobotListesi@RobotGuncelle')->middleware('Admin');
Route::any('ajax/Robot/TopluDurum','RobotListesi@TopluGuncelle')->middleware('Admin');
Route::any('ajax/RobotPara','RobotListesi@ParaEkrani')->middleware('Admin');
Route::any('ajax/RobotPara/Ekle','RobotListesi@Ekle')->middleware('Admin');
Route::any('ajax/RobotPara/Cikar','RobotListesi@Cikar')->middleware('Admin');
Route::any('ajax/RobotHesap/UpdateOlumsuzSorguTekrar','RobotListesi@OlumsuzSorguTekrar')->middleware('Admin');//


Route::any('ajax/RobotHesap/SistemiKapat','RobotListesi@SistemiKapat')->middleware('Admin');//
Route::any('ajax/RobotHesap/SistemiKapatYukleme','RobotListesi@SistemiKapatYukleme')->middleware('Admin');//

Route::any('ajax/RobotHesap/SureliIptalYukleme','RobotListesi@SureliIptalYukleme')->middleware('Admin');//


Route::any('ajax/RobotHesapHareketleri','RobotListesi@HesapHareketleri')->middleware('Rapor');
Route::any('ajax/RobotHesapHareketleri/temizle','RobotListesi@HesapHareketleriTemizle')->middleware('Rapor');

Route::any('ajax/RobotHesapHareketleri','RobotListesi@HesapHareketleri')->middleware('Yukleyici');
Route::any('ajax/RobotHesapHareketleri/temizle','RobotListesi@HesapHareketleriTemizle')->middleware('Yukleyici');
#endregion
#region diger
Route::get('ajax/iller','Api\AjaxApi@Iller');;
Route::get('ajax/ilce','Api\AjaxApi@Ilceler');
Route::any('ajax/yeniKullanici','Api\AjaxApi@YeniKullanici')->middleware('Admin');
Route::post('ajax/giris','Api\AjaxApi@KullaniciGiris')->middleware('Admin');
Route::get('ajax/cikis','Api\AjaxApi@KullaniciCikis')->middleware('Admin');
Route::any('tools/operator','Api\ToolsApi@DdOperator')->middleware('Admin');
Route::any('tools/tip','Api\ToolsApi@DdTip')->middleware('Admin');
Route::any('tools/robotTuru','Api\ToolsApi@DdRobotTuru')->middleware('Admin');
Route::any('tools/bayiler','Api\ToolsApi@DdBayiler')->middleware('Admin');
#endregion
#region Ipİslemleri
Route::any('ajax/SunucuIpEkle','IpIslemleri@SunucuEkle')->middleware('Admin');
Route::any('ajax/IsyeriIpEkle','IpIslemleri@IsyeriEkle')->middleware('Admin');
Route::any('ajax/IpSil','IpIslemleri@IpSil')->middleware('Admin');

#endregion
#region BayiListesi
Route::any('bayilistesi-bayiler/temizle','BayiListesi@Temizle')->middleware('Admin');
Route::any('ajax/BayiPara','BayiListesi@ParaEkrani')->middleware('Admin');
Route::any('bayilistesi-bayiler','BayiListesi@Bayiler')->middleware('Admin');
Route::any('ajax/BayiListesi/UpdateFiyat','BayiListesi@UpdateFiyat')->middleware('Admin');//
Route::any('ajax/BayiListesi/UpdateSw','BayiListesi@UpdateSw')->middleware('Admin');
Route::any('ajax/BayiPara/Cikar','BayiListesi@Cikar')->middleware('Admin');
Route::any('ajax/BayiPara/Ekle','BayiListesi@Ekle')->middleware('Admin');
Route::any('ajax/BayiHesapHareketleri','BayiListesi@HesapHareketleri')->middleware('Admin');
Route::any('ajax/BayiHesapHareketleri/temizle','BayiListesi@HesapHareketleriTemizle')->middleware('Admin');
Route::post('ajax/yeniKullanici/admin','BayiListesi@YeniKullanici')->middleware('Admin');
Route::any('ajax/KullaniciOzellikleri','BayiListesi@KullaniciOzellikleri')->middleware('Admin');
Route::post('ajax/KullaniciGuncelle','BayiListesi@KullaniciGuncelle')->middleware('Admin');
#endregion


#region KaraListe
Route::any('ayarlar-karaliste','KaraListeKontrol@Numaralar')->middleware('Admin');
Route::any('ajax/YeniKaraListe','KaraListeKontrol@YeniNumaraEkle')->middleware('Admin');
Route::any('ajax/KaraListeOzellikleri','KaraListeKontrol@KaraListeView')->middleware('Admin');
Route::any('ajax/KaraListe/UpdateSw','KaraListeKontrol@UpdateSw')->middleware('Admin');
Route::any('ajax/KaraListe/UpdateYuklemeSw','KaraListeKontrol@UpdateYuklemeSw')->middleware('Admin');
Route::any('ajax/KaraListe/DeleteSw','KaraListeKontrol@DeleteSw')->middleware('Admin');
#endregion

#region OzelAyarlar
Route::any('ayarlar-ozelayarlar','OzelAyarlar@Ayarlar')->middleware('Admin');
Route::any('ajax/OzelAyarlar/Update','OzelAyarlar@UpdateAyarlar')->middleware('Admin');

#endregion


#region Bankalar

Route::any('ayarlar-bankahesaplari','BankaListesi@Bankalar')->middleware('Admin');
Route::any('ajax/Bankalar/UpdateSw','BankaListesi@UpdateSw')->middleware('Admin');
Route::any('ajax/BankaOzellikleri','BankaListesi@BankaOzellikleri')->middleware('Admin');
Route::any('ajax/YeniBanka','BankaListesi@YeniBanka')->middleware('Admin');
Route::any('ajax/BankaGuncelle','BankaListesi@BankaGuncelle')->middleware('Admin');
#endregion
#region AdminAyar
Route::any('ayarlar-adminayarlari','AdminAyarlari@GetAdmin')->middleware('Admin');
Route::any('ajax/AdminAyar/Update','AdminAyarlari@UpdateAdmin')->middleware('Admin');
#endregion
#region PaketListesi
Route::any('kontor-paketlistesi/temizle','KontorPaketListesi@Temizle')->middleware('Admin');
Route::any('kontor-paketlistesi','KontorPaketListesi@PaketListesi')->middleware('Admin');
Route::any('ajax/PaketListesi/Update','KontorPaketListesi@PaketUpdate')->middleware('Admin');
Route::any('ajax/PaketListesi/UpdateProperty','KontorPaketListesi@UpdateProperty')->middleware('Admin');
Route::any('ajax/PaketListesi/TopluDurum','KontorPaketListesi@UpdateStatusAll')->middleware('Admin');
Route::any('ajax/YeniPaketEkle','KontorPaketListesi@YeniPaketEkle')->middleware('Admin');//
Route::any('ajax/PaketOzellikleri','KontorPaketListesi@PaketOzellikleri')->middleware('Admin');
Route::any('ajax/PaketListesi/exiptal','KontorPaketListesi@exiptal')->middleware('Admin');
Route::any('ajax/PaketOzellikleri/Guncelle','KontorPaketListesi@PaketOzellikleriGuncelle')->middleware('Admin');

#endregion

#region FiyatGruplari

Route::any('kontor-fiyatgruplari','KontorFiyatGruplari@FiyatGruplari')->middleware('Admin');
Route::any('ajax/FiyatGrubuEkle','KontorFiyatGruplari@GrupEkle')->middleware('Admin');
Route::any('ajax/YeniGrupEkle','KontorFiyatGruplari@GrupEklePOST')->middleware('Admin');
Route::any('kontor-grupduzenle','KontorFiyatGruplari@GrupDuzenle')->middleware('Admin');
Route::any('ajax/kontor-grupsil','KontorFiyatGruplari@GrupSil')->middleware('Admin');
Route::any('ajax/OzelPaketListesi/UpdateAktif','KontorFiyatGruplari@UpdateAktif')->middleware('Admin');
Route::any('ajax/OzelPaketListesi/UpdateSorgu','KontorFiyatGruplari@UpdateSorgu')->middleware('Admin');
Route::any('ajax/OzelPaketListesi/UpdateFiyat','KontorFiyatGruplari@UpdateFiyat')->middleware('Admin');
Route::any('ajax/OzelPaketListesi/UpdateTopluDurum','KontorFiyatGruplari@UpdateTopluDurum')->middleware('Admin');
Route::any('ajax/OzelPaketListesi/OtoFiyatDuzenle','KontorFiyatGruplari@OtoFiyatDuzenle')->middleware('Admin');
Route::any('kontor-grupduzenle/temizle','KontorFiyatGruplari@Temizle')->middleware('Admin');

#endregion

#region FaturaTakip

Route::any('fatura-faturatakip','FaturaTakip@getData')->middleware('Admin');
Route::any('fatura-faturatakip/temizle','FaturaTakip@Temizle')->middleware('Admin');
Route::any('ajax/FaturaTakip/Durum','FaturaTakip@DurumGuncelle')->middleware('Admin');
#endregion
#region YeniPaketler

Route::any('kontor-yenipaketler','KontorYeniPaketler@YeniPaketListesi')->middleware('Admin');
Route::any('ajax/YeniPaketler/TopluDurum','KontorYeniPaketler@DurumGuncelle')->middleware('Admin');
#endregion
#region yuklemetakip
Route::any('kontor-yuklemetakip','KontorYuklemeTakip@GetData')->middleware('Admin');
Route::any('kontor-yuklemetakip/temizle','KontorYuklemeTakip@Temizle')->middleware('Admin');
Route::any('ajax/YuklemeTakip/Durum','KontorYuklemeTakip@DurumGuncelle')->middleware('Admin');
#endregion

#region Kazanc_Takip

Route::any('kontor-kazanctakip','KontorKazancTakip@VeriCek')->middleware('Admin');
Route::any('kontor-kazanctakip/temizle','KontorKazancTakip@Temizle')->middleware('Admin');

#endregion

#region BayiNoHareket
Route::any('bayinohareket-kullanicihareket','BayiNoHareket@KullaniciHareketSorgula')->middleware('Admin');
Route::any('bayinohareket-bayihareket','BayiNoHareket@HareketSorgula')->middleware('Admin');
Route::any('bayinohareket-bayiler','BayiNoHareket@Bayiler')->middleware('Admin');
Route::any('bayinohareket-siteler','BayiNoHareket@Siteler')->middleware('Admin');
Route::any('ajax/BayiNoHareket/UpdateBayiBilgi','BayiNoHareket@BilgiGuncelle')->middleware('Admin');
Route::any('ajax/BayiNoHareket/UpdateBayiKategori','BayiNoHareket@KategoriGuncelle')->middleware('Admin');
Route::any('ajax/BayiNoHareket/Blokaj','BayiNoHareket@Blokaj')->middleware('Admin');
Route::any('ajax/BayiNoHareket/BlokajYukleme','BayiNoHareket@BlokajYukleme')->middleware('Admin');
Route::any('ajax/Siteler/Blokaj','BayiNoHareket@SitelerBlokaj')->middleware('Admin');
Route::any('ajax/Siteler/BlokajYukleme','BayiNoHareket@SitelerBlokajYukleme')->middleware('Admin');
Route::any('ajax/BayiNoHareket/temizle','BayiNoHareket@Temizle')->middleware('Admin');
Route::any('ajax/DisBayiler/temizle','BayiNoHareket@DisBayiTemizle')->middleware('Admin');
Route::any('ajax/KullaniciHareket/temizle','BayiNoHareket@KullaniciHareketTemizle')->middleware('Admin');
Route::any('ajax/Siteler/temizle','BayiNoHareket@SitelerTemizle')->middleware('Admin');

#endregion

#endregion
#region Api
#region VodafoneBotApi
Route::any('servis/bot_servis/bekleyen.php','Api\VodafoneBotApi@getNumbers');

Route::any('servis/bot_servis/feedback.php','Api\VodafoneBotApi@cevapRobot');


Route::any('servis/bot_servis/bot_login.php','Api\VodafoneBotApi@PacketList');
Route::any('servis/bot_servis/yeni_paket.php','Api\VodafoneBotApi@YeniPaketBildirimi');

#endregion
#region robotApi


Route::get('api/DownloadVersion','Api\RobotIndir@getDownload');
Route::get('api/ControlVersion','Api\RobotIndir@versiyonKontrol');

Route::get('api/Numbers','Api\RobotApi@getNumbers');

Route::get('api/Response','Api\RobotApi@cevapRobot');//


Route::get('api/PaketListesi','Api\RobotApi@PacketList');

Route::get('api/NewPacket','Api\RobotApi@NewPacket');

//Route::get('api/KayitBosalt','Api\RobotApi@KayitBosalt');

Route::get('api/paketAyar','Api\RobotApi@PaketAyar');
#endregion
#region ZnetApi
Route::any('servis/operator_listesi.php','Api\ServisZnet@paketListesi2');//http://localhost/RobotApiYeni/servis/operator_listesi.php?bayi_kodu=5325446303&sifre=123123
Route::any('servis/paket_listesi.php','Api\ServisZnet@paketListesi');//http://localhost/RobotApiYeni/servis/paket_listesi.php?bayi_kodu=5325446303&sifre=123123
Route::any('servis/tl_servis.php','Api\ServisZnet@istekYap');//http://localhost/RobotApiYeni/servis/tl_servis.php?bayi_kodu=5325446303&sifre=123123&operator=Turkcell&tip=firsat&kontor=288&gsmno=5325446303&tekilnumara=123123

Route::any('servis/bakiye_kontrol.php','Api\ServisZnet@bakiyeKontrol');//http://localhost/RobotApiYeni/servis/bakiye_kontrol.php?bayi_kodu=5325446303&sifre=123123

Route::any('servis/tl_kontrol.php','Api\ServisZnet@istekKontrol');//http://localhost/RobotApiYeni/servis/tl_kontrol.php?bayi_kodu=5325446303&sifre=123123&tekilnumara=123123
#region ZnetFatura
Route::any('servis/fatura_ekle.php','Api\ServisZnetFatura@istekYap');
Route::any('servis/fatura_kontrol.php','Api\ServisZnetFatura@istekKontrol');
Route::any('servis/kurum_listesi.php','Api\ServisZnetFatura@kurumListesi');
Route::any('servis/fatura_top_kontrol.php','Api\ServisZnetFatura@topluIstekKontrol');
#endregion
#endregion
#region ismailGencanApi
Route::any('api/kontor_yukle.php','Api\ServisGencan@istekYap');
Route::any('api/durum_sorgula.php','Api\ServisGencan@istekKontrol');
Route::any('api/tl_servis.php','Api\ServisGencan@istekYap');

Route::any('api/bakiye.php','Api\ServisGencan@bakiyeKontrol');

Route::any('api/tl_listesi.php','Api\ServisGencan@paketListesi');
#endregion
#region AVEA-DIS-SISTEM
Route::any('avea-api/','Api\ServisAvea@BaglantiDogrula');
#endregion

#region temizerApi
Route::any('services/talimat_bakiye_takip.php','Api\ServisTemizer@bakiyeKontrol');

Route::any('services/talimat_takip.php','Api\ServisTemizer@istekKontrol');

Route::any('services/talimat_takip2.php','Api\ServisTemizer@istekKontrol');

Route::any('services/talimat_ver.php','Api\ServisTemizer@istekYap');

Route::any('services/paket_listesi.php','Api\ServisTemizer@paketListesi');
#endregion
#region colakogluApi
Route::any('services/api.php','Api\ServisColakoglu@getParam');
#endregion
#region ServisApi
Route::any('bayiApi/paketListesi','Api\ServisApi@paketListesi');
Route::any('bayiApi/kayitOlustur','Api\ServisApi@istekYap');
Route::any('bayiApi/bakiyeSorgula','Api\ServisApi@bakiyeKontrol');
Route::any('bayiApi/kayitSorgula','Api\ServisApi@istekKontrol');
Route::any('bayiApi/faturaTalep','Api\ServisApiFatura@istekYap');
Route::any('bayiApi/faturaKontrol','Api\ServisApiFatura@istekKontrol');
Route::any('bayiApi/kurumListesi','Api\ServisApiFatura@kurumListesi');
#endregion
#endregion
