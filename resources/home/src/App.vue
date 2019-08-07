<template>
  <div id="app" ref="app">
    <Layout>
      <Header class="header">
        <Row class="tab">
          <i-col span="2">
            <span v-show="isLogin" class="titles" @click="showNav = !showNav">
              <Icon type="ios-book" size="24" color="white"></Icon>
            </span>
          </i-col>
          <i-col span="8" offset="6">{{route}}</i-col>
          <i-col span="2" offset="6">
            <Icon v-show="showSearh" type="ios-search" size="24" @click="tttt()"></Icon>
          </i-col>
        </Row>
      </Header>
    </Layout>
    <router-view @login="Login" @search="isSearch" class="view" :show="showInp" @scroll="scroll($event)"></router-view>
    <div v-show="showNav" @click="showNav = !showNav" id="mask"></div>
    <div class="navMenu" :class="showNav?'done':''">
      <div class="tabNav" v-for="(item,i) in str" :key="i" @click="navigate(item.name,i)">
        <p>{{item.name}}</p>
      </div>
      <div class="tabNav" @click="loginout">
        <p>安全退出</p>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "App",
    data() {
      return {
        showNav: false,
        str: [],
        route: "首页",
        first: false,
        showInp: false,
        showSearh: true,
        isLogin:true
      };
    },
    created() {
      var user = localStorage.getItem("userInfo");
      this.str = this.$router.options.routes;
      this.str.forEach((val,i)=>{
        if(val.name=='登录'){
          this.str.splice(i,1)
        }
      })
      if (user) {
        this.$router.push({ name: "首页" });
      } else {
        this.$router.push({ name: "登录" });
        this.route = ''
        this.showSearh = false
        this.isLogin =false
      }


    },
    methods: {
      navigate(e, i) {
        // this.str=[]
        this.route = this.str[i].name;
        this.showSearh = true;
        this.$router.push({ name: e, params: { name: this.str[i].name } });
        this.showInp = false;
        this.show();
      },
      show() {
        this.showNav = !this.showNav;
      },
      tttt() {
        this.showInp = !this.showInp;
      },
      scroll: r => {
      },
      isSearch(e) {
        this.showSearh = e;
      },
      Login(){
        this.$router.push({ path: "/" });
        this.route ='首页';
        this.showSearh = true
        this.isLogin =true
      },
      loginout(){
        localStorage.removeItem('userInfo')
        location.reload()
      }
    }
  };
</script>

<style>
  /* *{box-sizing: border-box;overflow: hidden;} */
  body,
  html {
    margin: 0;
    height: 100%;
    /* width: 100%; */
  }
  .iconfont {
    font-size: 24px;
    margin: 0 10px;
  }
  #mask{
    width: 100%;
    height:calc(100% - 64px);
    position: fixed;
    top: 64px;
    background: transparent;
    z-index: 1;

  }
  .icon-shanchu {
    color: red;
  }
  .view {
    height: 100%;
    overflow-y: scroll;
  }
  #app {
    font-family: "Avenir", Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    /* color: #2c3e50; */
    min-height: 100%;
    background: #f2f2f2;
    overflow: hidden;
    padding: 80px 0;
    padding-bottom: 0;
    height: 100%;
  }
  .titles {
    display: inline-block;
    width: 100%;
    text-align: left;
    line-height: 15px;
    padding-left: 10px;
    border-left: 3px solid #18a689;
  }
  .ivu-layout {
    width: 100%;
    background: transparent;
    position: fixed;
    top: 0;
    z-index: 999;
  }
  .ivu-dropdown-item {
    padding: 10px 0;
  }
  .ivu-dropdown-menu,
  .ivu-dropdown-menu > * {
    width: 100% !important;
  }
  .ivu-dropdown-menu > * {
    text-align: center;
    margin: 5px 0;
  }
  .done {
    transform: translateX(0) !important;
  }
  .drop {
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .navMenu {
    background: white;
    position: fixed;
    top: 64px;
    transform: translateY(-50%);
    transform: translateX(-100%);
    left: 0;
    padding: 20px 30px;
    box-sizing: border-box;
    transition: all 0.1s linear;
    background: rgb(81, 90, 110);
    z-index: 999;
  }
  .navMenu .tabNav {
    width: 100%;
    height: 50px;
    /* display: inline-block; */
    color: white;
    line-height: 50px;
  }
  .navMenu .tips {
    position: absolute;
    right: -50px;
    top: 50%;
    transform: translateY(-50%);
  }
  .tab {
    width: 100%;
    height: 100%;
    color: white;
  }
  .header {
    height: 64px;
    /* background: #657180; */
    display: flex;
    align-items: center;
    padding: 0 1rem;
  }
  div.ivu-col.box {
    height: 40px;
    box-sizing: border-box;
    /* border-bottom: 1px solid rgba(245, 245, 245, 0.5) */
  }
  /* div.ivu-col.box:nth-child(2n){
    border-left: 1px solid rgba(245, 245, 245, 0.5)
  } */
  div.ivu-col.box:last-child {
    width: 100%;
  }
  div button {
    margin: 0 1rem;
  }
  div.ivu-col.box.time {
    width: 100% !important;
    border-left: none;
  }
  div.ivu-col.box > div {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 5px;
    box-sizing: border-box;
  }
  div.ivu-col.box:last-child div.ivu-col.box:last-child {
    width: 100%;
  }
  div.ivu-col.box > div > span {
    display: inline-block;
    width: 50%;
    font-size: 0.7rem;
    text-align: left;
  }
  div.ivu-row > div:nth-child(2n-1) > span:first-child {
    color: rgb(185, 185, 185);
  }
  div.ivu-col.box > div > span button {
    margin: 0;
  }
  .header a {
    color: white;
  }
  .Breadcrumb {
    color: #657180;
    background: transparent;
    text-align: center;
    /* margin: 20px 0; */
    width: 100%;
    height: 100%;
    text-align: left;
  }
  .ivu-dropdown {
    width: 100%;
    height: 100%;
    text-align: right;
  }
  .layout-logo {
    width: 3rem;
    height: 3rem;
    display: inline-block;
    background: url(assets/profile_small.jpg) no-repeat;
    background-size: cover;
    background-position: center;
    border-radius: 50%;
    vertical-align: middle;
  }

  .text_over {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
  }
  .ivu-page {
    margin-top: 30px;
  }
  .ivu-card{
    width: 95%!important;
  }
  /* .ivu-card .ivu-row > .ivu-col:nth-child(2) {
    border-right: 1px solid rgba(245, 245, 245, 0.5);
  } */
  .options,
  .ivu-card-body > button {
    border: none !important;
    margin-top: 10px;
  }
  .ivu-card {
    border-radius: 10px;
  }
  /* .ivu-row:not(:last-child) {
    border-bottom: 1px solid rgba(245, 245, 245, 0.5);
  } */
  .ivu-form.form {
    padding: 0 10px;
  }
  .pass button {
    margin: 5px 0;
  }
  .ts{
    margin-bottom: 10px
  }
</style>
