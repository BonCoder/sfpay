<template>
  <div class="layout-content" @scroll="scroll($event)">
    <i-form inline v-show="!status&&!payStatus&&show">
      <Form-item>
        <i-input v-model="query.name" placeholder="输入需要查询的会员/公司名称"></i-input>
      </Form-item>
      <Form-item>
        <i-button type="success" icon="ios-search" @click="search">查询</i-button>
      </Form-item>
      <Form-item>
        <i-button type="success" @click="format()" ghost>新建会员</i-button>
      </Form-item>
    </i-form>
    <Card v-show="!status&&!payStatus" v-for="(props,index) in list " :key="index">
      <div class="ivu-col box" v-for="(item,idx) in props " :key="idx" v-show="!item.time">
        <div>
          <span class="span-1">{{item.name}}</span>
          <span  class="span-2" v-show="item.value!=='status'">{{item.value}}</span>
          <i-button v-show="item.value=='status'" size="small" type="success">开启</i-button>
        </div>
      </div>
      <div class="ivu-col box time">
        <div>
          <span class="span-1">上次操作时间</span>
          <span class="span-2">{{props[props.length-1].value}}</span>
        </div>
      </div>
      <div class="options">
        <i-button icon="md-settings" @click="format(props)" size="small">设置</i-button>
        <i-button icon="logo-yen" type="primary" size="small" @click="topay(props)" ghost>充值</i-button>
        <i-button icon="ios-trash-outline" size="small" type="error" @click="del(props.id)">删除</i-button>
      </div>
    </Card>
    <i-form v-show="payStatus" class="form">
      <Form-item label="充值金额">
        <i-input v-model="pay.money" placeholder="请输入充值金额"></i-input>
      </Form-item>
      <Form-item label="备注">
        <i-input  v-model="pay.remark" placeholder="请输入备注" type="textarea"></i-input>
      </Form-item>
      <Form-item>
        <i-button type="success" size="large" icon @click="pays">
          <Icon type="ios-checkmark" size="24" />确定
        </i-button>
        <i-button type="error" size="large" @click="payStatus=false">
          <Icon type="ios-close" size="24" />返回
        </i-button>
      </Form-item>
    </i-form>
    <i-form v-show="status" class="form">
      <Form-item v-for="(item,idx) in formData " :key="idx" :label="item.name">
        <i-input v-model="formData[idx][item.key]" :placeholder="'请输入'+item.name" v-if="!item.type"></i-input>
        <Upload v-if="item.type==1" style="float:left" action="http://www.shenfupay.net/admin/upload/uploadface" name='file' :on-error="uploadError" :on-success="upload"> 
          <i-button v-if="item.type ==1" size="default" type="primary">选择头像</i-button>
        </Upload>
      </Form-item>
      <Form-item>
        <i-button type="success" size="large" icon @click="confirm">
          <Icon type="ios-checkmark" size="24" />确定
        </i-button>
        <i-button type="error" size="large" @click="cancel">
          <Icon type="ios-close" size="24" />返回
        </i-button>
      </Form-item>
    </i-form>
    <!-- <Page :total="40" size="small" show-total></Page> -->
  </div>
</template>

<script>
export default {
  name: "HelloWorld",
  props: ["show"],
  data() {
    return {
      status: false,
      list: [],
      query: {},
      pay: {},
      payStatus: false,
      page: 0,
      formData: [],

    };
  },
  created() {
    let that = this;
    setInterval(() => {
      let time = new Date();
      that.msg = time.toLocaleString();
    }, 1000);
    this.getData();
    console.log(this.formData);
  },
  methods: {
    upload(e){
      console.console(e)
    },
    uploadError(e){
      this.$Message.error('上传失败，请重试或检查网络')
    },
    pays(){
      let that =this
      this.$ajax({
        url:"member/recharge",
        data:this.pay,
        method:"POST",
        then:r=>{
          that.$Message.info('充值成功')
          that.status = false
          that.pay={}
        }
      })
    },
    del(id){
      let that =this
      this.$ajax({
        url:"member/del_member",
        data:{id:id},
        method:"post",
        then:r=>{
          that.$Message.info('操作成功')
          thta.getData()
        }
      })
    },
    back() {
      this.status = false;
    },
    topay(id) {
      this.pay.id = id[0].id
      this.payStatus = true;
    },
    cancel() {
      this.status = false;
      this.formData = [];
    },
    search() {
      this.page = 0;
      this.getData();
    },
    getData(yt) {
      let that = this;
      this.$ajax({
        url: "member/lists",
        method: "get",
        data: {
          limit: 10,
          offset: this.page * 10,
          username: this.query.name
        },
        then: r => {
          if (r.code != 1) {
            this.$Message.info(r.data.msg);
          }
          let list = [];
          r.data.forEach((val, i) => {
            let arr = [
              { name: "账号", value: val.account, check: true ,key:"account",id:val.id},
              { name: "公司", value: val.nickname ,check: true ,key:"nickname"},
              { name: "费率", value: val.rate, check: true ,key:"rate"},
              { name: "余额", value: val.money },
              { name: "开户名", value: val.bank_owner, check: true  ,key:"bank_owner"},
              { name: "银行卡", value: val.bank_card, check: true ,key:"bank_card"},
              { name: "开户行", value: val.bank_name, check: true  ,key:"bank_name"},
              { name: "状态", value: val.status == 1 ? "正常" : "禁用" },
              { name: "上次操作时间	", value: val.last_login_time, time: true }
            ];
            list.push(arr);
          });
          if (yt) {
            that.list.concat(list);
          } else {
            that.list = list;
          }
        }
      });
    },
    format(props) {
      console.log(props);
      let arr = [
        { name: "公司名称", value:'', check: true ,key:"nickname"},
        { name: "费率", value: '', check: true ,key:"rate"},
        { name: "开户名", value: '', check: true  ,key:"bank_owner"},
        { name: "银行卡号", value: '', check: true ,key:"bank_card"},
        { name: "开户行", value:'', check: true ,key:"bank_name"},
      ];
      let p = props ? [...props] : [...arr];
      p.forEach(val => {
        let obj = {};
        obj.name = val.name;
        obj.value = val.value;
        obj.key =val.key
        if (!props) {
          obj.value = "";
        }else{
          obj[obj.key] = obj.value
        }
        if (val.check) {
          this.formData.push(obj);
        }
      });
      this.formData.push({ name: "密码", value: "password" ,key:"password" });
      this.formData.push({ name: "交易密码", value: "payPassword" ,key:"pay_password"});
      this.formData.push({ name: "头像", value: "avatar", type: "1"  });
      this.status = true;
      console.log(this.list);
    },
    confirm(){
      let obj ={}
      let that = this
      this.formData.forEach(val=>{
        obj[val.key] = val[val.key]
      })
      this.$ajax({
        url:"member/save",
        data:obj,
        method:"post",
        then:r=>{
          that.cancel()
          that.getData()
        }
      })
      console.log(obj)
    },
    scroll(e) {
      let windowHeight =
        document.documentElement.clientHeight || document.body.clientHeight;
      if (
        windowHeight + e.currentTarget.scrollTop >=
          e.currentTarget.scrollHeight &&
        !this.status
      ) {
        this.page++;
        this.getData(true);
      }
    }
  },
  computed: {}
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
html {
  /* min-height: 100%; */
  /* background: #f2f2f2; */
}
.layout-content {
  min-height: 100%;
  width: 100%;
  background: #f2f2f2;
  padding-top: 20px;
}
.ivu-card {
  width: 80%;
  /* height: 100px; */
  /* display: inline-block; */
  margin: 0 auto;
  margin-bottom: 20px;
}
.options {
  border: none !important;
  margin-top: 10px;
  display: flex !important;
  justify-content: center;
}
</style>
