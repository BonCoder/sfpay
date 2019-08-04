<template>
  <div @scroll="scroll($event)">
    <i-form inline v-show="show">
      <Form-item>
        <i-input :value.sync="formItem.input" placeholder="请输入卡号"></i-input>
      </Form-item>
      <Form-item>
        <i-input :value.sync="formItem.input" placeholder="请输入金额" number></i-input>
      </Form-item>
      <Form-item>
        <i-input :value.sync="formItem.input" placeholder="请输入身份证" number></i-input>
      </Form-item>
      <Form-item>
        <i-input :value.sync="formItem.input" placeholder="请输入姓名" ></i-input>
      </Form-item>
    </i-form>
    <i-button type="success" size="large">查询</i-button>
    <i-button type="default" size="large">重置</i-button>
    <Card v-for="(item,i) in arr" :key="i">
      <Row>
        <i-col class="tables" span="6">
          <span>批次号</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.account}}</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>用户名</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.company}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="6">
          <span>公司名称</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.company}}</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>文件名称</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.company}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="6">
          <span>总笔数</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.company}}</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>总金额</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.company}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="6">
          <span>上传日期</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.company}}</span>
        </i-col>
      </Row>
      <!-- <Row>
        <i-col class="tables options" span="12">
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
   props:['show'],
  data() {
    return {
      visible: false,
      name: "",
      formItem:{},
      arr: [
        {
          company: "admin",
          total: 132,
          remark: "POS交易款存入",
          type: "充值"
        },
        {
          remark: "POS交易款存入",
          type: "充值",
          company: "tencent",
          total: 130
        },
        {
          remark: "POS交易款存入",
          type: "充值",
          company: "bitdance",
          total: 130
        },
        {
          remark: "POS交易款存入",
          type: "充值",
          company: "bitdance",
          total: 130
        },
        {
          remark: "POS交易款存入",
          type: "充值",
          company: "bitdance",
          total: 130
        },
        {
          remark: "POS交易款存入",
          type: "充值",
          company: "bitdance",
          total: 130
        },
        {
          remark: "POS交易款存入",
          type: "充值",
          company: "bitdance",
          total: 130
        },
        {
          remark: "POS交易款存入",
          type: "充值",
          company: "bitdance",
          total: 130
        }
      ]
    };
  },
  created() {
    this.name = this.$route.params.name;
    this.arr.forEach(val => {
      val.visible = false;
    });
  },
  mounted() {},
  methods: {
    edit() {},
    scroll(){}
  }
};
</script>
<style >
.ivu-row:not(:last-child) {
  border-bottom: 1px solid rgba(245, 245, 245, 0.5);
}

form {
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

