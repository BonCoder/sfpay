<template>
  <div class="layout-content" @scroll="scroll($event)" ref="wrap">
    <i-form inline v-show="show">
      <Form-item>
        <i-input v-model="query.account" placeholder="请输入账号" number></i-input>
      </Form-item>
      <Form-item>
        <i-input v-model="query.money" placeholder="请输入金额" number></i-input>
      </Form-item>
      <Form-item>
        <i-input v-model="query.bank_card" placeholder="请输入卡号" number></i-input>
      </Form-item>
      <Form-item>
        <i-input v-model="query.shenfenzheng" placeholder="请输入身份证" number></i-input>
      </Form-item>
      <Form-item>
        <i-input v-model="query.bank_name" placeholder="开户行" number></i-input>
      </Form-item>
      <i-button type="success" size="large" @click="search">
        <Icon type="ios-search"></Icon>
        <span>搜索</span>
      </i-button>
    </i-form>
    <Card v-show="!status" v-for="(props,index) in list " :key="index">
      <div class="ivu-col box" v-for="(item,idx) in props.list " :key="idx" >
        <div>
          <span class="span-1">{{item.name}}</span>
          <span class="span-2">{{item.value}}</span>
        </div>
      </div>
      <div class="ts" v-show="props.status==1">一审操作</div>
      <div class="ivu-col box time" v-show="props.status==1" style="margin-top: 5px">
        <i-button type="success" @click="examine(3,props.id)">通过</i-button>
        <i-button type="error" @click="examine(2,props.id)">不通过</i-button>
      </div>
      <div class="ts" v-show="props.status==3">二审操作</div>
      <div class="ivu-col box time" v-show="props.status==3" style="margin-top: 5px">
        <i-button type="success" @click="examine(5,props.id)">通过</i-button>
        <i-button type="error" @click="examine(4,props.id)">不通过</i-button>
      </div>
    </Card>
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
      query: {limit:10},
      page:0,
      arr: [
        { name: "账号", value: "acount", check: true },
        { name: "公司名称", value: "company" },
        { name: "费率", value: "payParsent", check: true },
        { name: "余额", value: "balance" },
        { name: "开户名", value: "bankCardName", check: true },
        { name: "银行卡号", value: "card", check: true },
        { name: "开户行", value: "bank", check: true },
        { name: "状态", value: "status" },
        { name: "上次操作时间	", value: "lastTime", time: true }
      ],
      formItem: {}
    };
  },
  created() {
    let that = this;
    setInterval(() => {
      let time = new Date();
      that.msg = time.toLocaleString();
    }, 1000);
    this.getData()
  },
  methods: {
    scroll(e) {
      let [sH, sT, cH] = [
        e.currentTarget.scrollHeight,
        e.currentTarget.scrollTop,
        e.currentTarget.clientHeight
      ];
      if (sH == sT + cH) {
        this.page++;
        this.query.offset = 10*this.page
        this.getData(true);
      }
    },
    search(){
      this.page=0,
      this.query.offset =0
      this.getData()
    },
    examine(s,i){
      let that = this
      this.$ajax({
        url:"daifu/changeStatus",
        method: 'post',
        data:{
          id:i,
          status:s
        },
        then:r=>{
          this.$Message.success('审核成功');
          this.page=0;
          this.query.offset =0;
          that.getData()
        }
      })
    },
    getData(t) {
      let that = this
     this.$ajax({
       url:'daifu/examine',
       data:this.query,
       then:r=>{
         let list =[]
         r.data.forEach(val=>{
           let str;
           switch (val.status) {
             case 1:
               str='待审核'
               break;
            case 5:
               str='代付成功'
               break;
            case 2:
            str='初审未通过'
            break;
            case 3:
            str='初审通过'
            break;
            case 4:
            str='终审未通过'
            break;
            case 6:
            str='转账成功'
            break;
           }
           let obj={}
           let arr= [
              { name: "账号", value: val.account, check: true },
              { name: "公司", value: val.nickname },
              { name: "金额", value: val.money  },
              { name: "开户名", value:val.bank_owner, check: true },
              { name: "卡号", value:val.bank_card, check: true },
              { name: "开户行", value:val.bank_name, check: true },
              { name: "身份证", value:val.shenfenzheng, check: true },
              { name: "状态", value: str},
              { name: "时间	", value: val.create_time, time: true }
            ]
            obj.list =arr
            obj.status = val.status
            obj.id = val.id
            list.push(obj)
         })
         if(t){
           that.list = that.list.concat(list)
         }else{
           that.list =list
         }
       }
     })
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
  /* display: inline-block; */
  margin: 0 auto;
  margin-bottom: 20px;
}
</style>
