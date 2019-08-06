<template>
  <div @scroll="scroll($event)">
    <Card shadow>
      <Row class="tables">
        <i-col class="tables" span="12">
          <span>账号</span>
        </i-col>
        <i-col class="tables" span="12">
          <span>{{item.account}}</span>
        </i-col>
      </Row>
      <Row class="tables">
        <i-col class="tables" span="12">
          <span>公司名称</span>
        </i-col>
        <i-col class="tables" span="12">
          <span>{{item.nickname}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="12">
          <span>费率</span>
        </i-col>
        <i-col class="tables" span="12">
          <span>{{item.rate}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="12">
          <span>余额</span>
        </i-col>
        <i-col class="tables" span="12">
          <span>{{item.money}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="12">
          <span>开户行</span>
        </i-col>
        <i-col class="tables" span="12">
          <span>{{item.bank_name}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="12">
          <span>开户名</span>
        </i-col>
        <i-col class="tables" span="12">
          <span>{{item.bank_owner}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="12">
          <span>银行卡号</span>
        </i-col>
        <i-col class="tables" span="12">
          <span>{{item.bank_card}}</span>
        </i-col>

      </Row>
      <Row>
        <i-col class="tables" span="12">
          <span>上次操作时间</span>
        </i-col>
        <i-col class="tables" span="12">
          <span>{{item.last_login_time}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="12">
          <span>状态</span>
        </i-col>
        <i-col class="tables" span="12">
          <span>{{item.status}}</span>
        </i-col>
      </Row>
      <!-- <Row>
        <i-col class="tables" span="12">
          <i-button icon="md-settings" shape="circle">设置</i-button>
        </i-col>
        <i-col class="tables options" span="12">
          <i-button icon="ios-trash-outline" type="error" shape="circle">删除</i-button>
        </i-col>
      </Row> -->
    </Card>
    <!-- <Page :total="40" size="small" show-total></Page> -->
  </div>
</template>
<script>
export default {
  data() {
    return {
      visible: false,
      name: "",
      page:0,
      item:{}
    };
  },
  created() {
    this.name = this.$route.params.name;

    this.$emit('search',false)
    this.getData()
  },
  mounted() {},
  methods: {
    getData(t){
      let  that = this
      this.$ajax({
        url:"chongzhi/total",
        data:{
          limit:10,
          offset:this.page*10
        },
        then:r=>{
          that.item = r.data
        }
      })
    },
    edit() {},
    scroll(e){
    }
  }
};
</script>
<style scoped>
.ivu-card {
  width: 90%;
  margin: 30px auto;
  margin-bottom: 0;
}
.iconfont {
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

