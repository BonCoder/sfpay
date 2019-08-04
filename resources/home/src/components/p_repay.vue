<template>
  <div @scroll="scroll($event)">
    <i-form inline v-show="show">
      <Form-item>
        <i-input v-model="query.bank_card" placeholder="请输入卡号"></i-input>
      </Form-item>
      <Form-item>
        <i-input v-model="query.money" placeholder="请输入金额" number></i-input>
      </Form-item>
      <Form-item>
        <i-input  v-model="query.shenfenzheng"  placeholder="请输入身份证" number></i-input>
      </Form-item>
      <Form-item>
        <i-input  v-model="query.bank_owner" placeholder="请输入开户行"></i-input>
      </Form-item>
      <i-button type="success" size="large" @click="search">查询</i-button>
      <i-button type="default" size="large" @click="reset">重置</i-button>
    </i-form>
    <Card v-for="(item,i) in arr" :key="i">
      <Row class="tables">
        <i-col class="tables" span="6">
          <span>账号</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.account}}</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>公司名称</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.nickname}}</span>
        </i-col>
      </Row>
      <Row class="tables">
        <i-col class="tables" span="6">
          <span>开户名</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.bank_owner}}</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>操作金额</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.money}}</span>
        </i-col>
      </Row>
      <Row>
          <i-col class="tables" span="6">
            <span>银行卡号</span>
          </i-col>
          <i-col class="tables" span="6">
            <span>{{item.bank_card}}</span>
          </i-col>
          <i-col class="tables" span="6">
          <span>身份证号</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.shenfenzheng}}</span>
        </i-col>
        </Row>
      <Row>
        <i-col class="tables" span="6">
          <span>状态</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.str}}</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>操作时间</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.create_time}}</span>
        </i-col>
      </Row>
      <!-- <Row>
        <i-col class="tables options" span="12" @click="setting">
          <i-button icon="md-settings" shape="circle" >设置</i-button>
        </i-col>
        <i-col class="tables options" span="12">
          <i-button icon="ios-trash-outline" type="error" shape="circle">删除</i-button>
        </i-col>
      </Row>-->
    </Card>
    <!-- <Page :total="40" size="small" show-total></Page> -->
  </div>
</template>
<script>
export default {
  props: ["show"],
  data() {
    return {
      visible: false,
      name: "",
      formItem: {},
      arr: [],
      page:0,
      query:{}
    };
  },
  created() {
    this.name = this.$route.params.name;
    this.getData();
  },
  mounted() {},
  methods: {
    setting() {},
    getData(t) {
      let that = this;
      let data = this.query
      data.offset = this.page*10
      data.limit = 10
      this.$ajax({
        url: "daifu/member_index",
        data: data,
        then: r => {
          r.data.forEach(val => {
            val.visible = false;
            let str;
            switch (val.status) {
              case 1:
                str = "待审核";
                break;
              case 2:
                str = "初审未通过";
                break;
              case 3:
                str = "初审通过";
                break;
              case 4:
                str = "终审未通过";
                break;
              case 5:
                str = "代付成功";
                break;
              case 6:
                str = "转账成功";
                break;
            }
            val.str = str
          });
          if (t) {
            that.arr = that.arr.concat(r.data);
          } else {
            that.arr = r.data;
          }
        }
      });
    },
    reset(){
      for(let key in this.query){
        this.query[key]=''
      }
    },
    errors(e) {
      console.log(e);
    },
    sucess(e) {
      console.log(e);
    },
    search(){
      this.page = 0
      this.getData()
    },
    bUpload(e) {
      console.log(e);
    },
    scroll(e) {
      let windowHeight =
        document.documentElement.clientHeight || document.body.clientHeight;
      if (
        windowHeight + e.currentTarget.scrollTop >=
          e.currentTarget.scrollHeight
      ) {
        this.page++;
        this.getData(true);
      }
    }
  }
};
</script>
<style >
form {
  margin-top: 10px;
}
.ivu-upload {
  margin-top: 10px;
}
.ivu-form-item:nth-child(1),
.ivu-form-item:nth-child(2) {
  margin-top: 30px;
}
button {
  /* margin: 30px; */
}
.ivu-card {
  width: 90%;
  margin: 30px auto;
  margin-bottom: 0;
}
.ivu-row {
  height: 50px;
}
.tables {
  height: 50px;
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
}
.tables span {
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}
.del {
  color: red;
}
</style>

