<template>
    <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Yeni Kayıt</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="app"
                        ref="yeniKullaniciForm"
                        action="vue/yeniKullanici"
                        method="post" >
                        <div class="form-group " v-bind:class="{  'has-error has-feedback': isimErr }">
                            <label class="col-sm-2 control-label" for="isim"> İsim: </label>
                            <div class="col-sm-10">
                                <input ref="isim" name="isim" id="isim" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{  'has-error has-feedback': soyadErr }">
                            <label class="col-sm-2 control-label" for="soyad"> Soyad: </label>
                            <div class="col-sm-10">
                                <input ref="soyad" name="soyad" id="soyad" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{  'has-error has-feedback': takmaAdErr }">
                            <label class="col-sm-2 control-label" for="takmaAd"> Kullanıcı Adı: </label>
                            <div class="col-sm-10">
                                <input ref="takmaAd" name="takmaAd" id="takmaAd" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{  'has-error has-feedback': sifreErr }">
                            <label class="col-sm-2 control-label" for="sifre1">Sifre: </label>
                            <div class="col-sm-10">
                                <input  ref="sifre1" name="sifre1" id="sifre1" type="password" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{  'has-error has-feedback': sifreTErr }">
                            <label class="col-sm-2 control-label" for="sifreT">Sifre Tekrar: </label>
                            <div class="col-sm-10">
                                <input ref="sifreT" name="sifreT" id="sifreT" type="password" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{  'has-error has-feedback': firmaAdiErr }">
                            <label class="col-sm-2 control-label" for="firmaAdi"> Firma Adı: </label>
                            <div class="col-sm-10">
                                <input ref="firmaAdi" name="firmaAdi" id="firmaAdi" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{  'has-error has-feedback': mailErr }">
                            <label class="col-sm-2 control-label" for="mail"> Mail: </label>
                            <div class="col-sm-10">
                                <input ref="mail" name="mail" id="mail" type="email" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="sabitTel"> Sabit Tel: </label>
                            <div class="col-sm-10">
                                <input ref="sabitTel" name="sabitTel" id="sabitTel" type="number" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{  'has-error has-feedback': cepTelErr }">
                            <label class="col-sm-2 control-label" for="cepTel"> Cep No: </label>
                            <div class="col-sm-10">
                                <input ref="cepTel" name="cepTel" id="cepTel" type="tel" maxlength="10"  placeholder="5__ ___ __ __" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="vergiDairesi"> Vergi Dairesi: </label>
                            <div class="col-sm-10">
                                <input ref="vergiDairesi" name="vergiDairesi" id="vergiDairesi" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="vergiNo"> Vergi Numarası: </label>
                            <div class="col-sm-10">
                                <input ref="vergiNo" name="vergiNo" id="vergiNo" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="adres"> Adres: </label>
                            <div class="col-sm-10">
                                <input ref="adres" name="adres" id="adres" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{  'has-error has-feedback': ilErr }">
                            <label class="col-sm-2 control-label" for="il"> İl: </label>
                            <div class="col-sm-10">
                                <select class="form-control" name="il" id="il" ref="il"  @change="onChange" >
                                    <option value="0">Seçiniz</option>
                                    <option v-for="Result in Results" v-bind:key="Result.id" :value="Result.id">{{Result.adi}} </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{  'has-error has-feedback': ilceErr }">
                            <label class="col-sm-2 control-label" for="ilce"> İlçe: </label>
                            <div class="col-sm-10">
                               <loader v-if="loading"></loader>
                               <select v-else class="form-control" name="ilce" id="ilce"  ref="ilce"  >
                                    <option value="0">Seçiniz</option>
                                    <option v-for="Result in ilceResults" v-bind:key="Result.id" :value="Result.id" >{{Result.adi}}  </option>
                                </select>
                            </div>
                        </div>
                         <input type="hidden" name="_token" :value="csrf">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="sendForm" style="background-color: green;">Kaydol</button>
                </div>
            </div>
</template>



<script>
    export default {
        data() {
           return{
               csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
               isimErr:false,
               soyadErr:false,
               takmaAdErr:false,
               sifreErr:false,
               sifreTErr:false,
               firmaAdiErr:false,
               mailErr:false,
               cepTelErr:false,
               ilErr:false,
               ilceErr:false,

               loading:false,
               ilceResults:[],
               Results:[],
               Result:{
                   id:'',
                   adi:'',
                   
               }
           }
        },
        mounted()
        {
            
           this.fetchprovince();

        },
        methods:{
             fetchprovince()
            {
                fetch('vue/iller').
                then(res=>res.json()).
                then(res=>{
                    this.Results=res.Results;
                });
                
                
            }
            ,
            onChange()
            {
                this.loading=true;
                var ilId=this.$refs.il.value
                console.log(ilId);
                fetch('vue/ilce?id='+ilId).
                then(res=>res.json()).
                then(res=>{
                    console.log(res);
                    this.ilceResults=res.Results;
                    this.loading=false;
                });
            },
            sendForm(e)
            {
                if(!this.validateForm())
                {
                    return;
                }
                console.log("validate success");
                Vue.axios['post']('vue/yeniKullanici', new FormData(this.$refs.yeniKullaniciForm))
                .then(res=>res.data).then(res=>
                {
                    console.log(res.status);
                    if(res.status=="true")
                    {
                        toastr.success(res.message);
                    }
                    else
                    {
                        toastr.error(res.message);
                    }
                })
                .catch(error => console.log(error));
            },
            validateForm()
            {
                var validate=true;
                var isim=this.$refs.isim.value;
                var soyad=this.$refs.soyad.value;
                var firmaAdi=this.$refs.firmaAdi.value;
                var cepTel=this.$refs.cepTel.value;
                var takmaAd=this.$refs.takmaAd.value;
                var sifre1=this.$refs.sifre1.value;
                var sifreT=this.$refs.sifreT.value;
                var mail=this.$refs.mail.value;
                var il=this.$refs.il.value;
                var ilce=this.$refs.ilce.value;
                
                if(isim.length<2 || isim.length>20)
                {
                    toastr.error("İsim alanı 2 karakterden uzun 20 karakterden küçük olmalıdır!");
                    this.isimErr=true;
                    validate=false;
                }
                if(soyad.length<2 || soyad.length>20)
                {
                    toastr.error("Soyad alanı 2 karakterden uzun 20 karakterden küçük olmalıdır!");
                    this.soyadErr=true;
                    validate=false;
                }
                if(firmaAdi.length<4 || firmaAdi.length>40)
                {
                    toastr.error("Firma Adi alanı 4 karakterden uzun 40 karakterden küçük olmalıdır!");
                    this.firmaAdiErr=true;
                    validate=false;
                }
                if(cepTel.length!=10 )
                {
                    toastr.error("Cep No alanı başında '0' olmadan '10' haneli telefon numaranızı girmenizi istemektedir!");
                    this.cepTelErr=true;
                    validate=false;
                }
                if(takmaAd.length<4 || takmaAd.length>21 )
                {
                    toastr.error("Kullanıcı Adı alanı 5 ila 20 karakter aralığında olmalıdır !");
                    this.takmaAdErr=true;
                    validate=false;
                }
                if(sifre1.length<5 )
                {
                    toastr.error("Şifre alanı en az 6 karakter olabilir ! ");
                    this.sifreErr=true;
                    validate=false;
                }
                if(sifre1 != sifreT )
                {
                    toastr.error("Şifreleriniz uyuşmamaktadır ! ");
                    this.sifreErr=true;
                    this.sifreTErr=true;
                    validate=false;
                }
                if(sifre1 != sifreT )
                {
                    toastr.error("Şifreleriniz uyuşmamaktadır ! ");
                    this.sifreErr=true;
                    this.sifreTErr=true;
                    validate=false;
                }
                var re = /\S+@\S+\.\S+/;
                var mailValidate= re.test(mail);
                if(!mailValidate)
                {
                    console.log(mail);
                    console.log(mailValidate);
                    toastr.error("Lütfen Uygun bir Mail Adresi Giriniz ! ");
                    this.mailErr=true;
                    validate=false;
                }
                    
                if(il==0)
                {
                    toastr.error("Lütfen il seçiniz! ");
                    this.ilErr=true;
                    validate=false;
                }
                if(ilce==0)
                {
                    toastr.error("Lütfen ilçe seçiniz! ");
                    this.ilceErr=true;
                    validate=false;
                }
                return validate;
                
            }
        }
    }
</script>