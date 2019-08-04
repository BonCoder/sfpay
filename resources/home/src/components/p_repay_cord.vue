<template>
  <div @scroll="scroll($event)">
    <i-form inline v-show="show">
      <Form-item>
        <i-input v-model="query.filename" placeholder="请输入文件名"></i-input>
      </Form-item>
      <Form-item>
        <i-input v-model="query.money" placeholder="请输入金额" number></i-input>
      </Form-item>
      <Form-item>
        <i-input v-model="query.count" placeholder="请输入总笔数" number></i-input>
      </Form-item>
      <Form-item>
        <i-input v-model="query.picihao" placeholder="请输入批次号" number></i-input>
      </Form-item>
      <i-button type="success" size="large" @click="search">查询</i-button>
      <i-button type="default" size="large" @click="reset">重置</i-button>
      <Upload
        action="http://www.shenfupay.net/api/daoru/daoru"
        @on-error="errors"
        @before-upload="bUpload"
        type="select"
        @on-success="sucess()"
      >
        <i-button type="ghost" size="large" accept="xlsx" style="color:green">导入EXCEL文件</i-button>
      </Upload>
    </i-form>

    <Card v-for="(item,i) in arr" :key="i">
      <Row>
        <i-col class="tables" span="6">
          <span>批次号</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.picihao}}</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>用户名</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.account}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="6">
          <span>公司名称</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.nickname}}</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>文件名称</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.filename}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="6">
          <span>总笔数</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.count}}</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>总金额</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.money}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="6">
          <span>上传日期</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.create_time}}</span>
        </i-col>
      </Row>
      <!-- <Row>
        <i-col class="tables options" span="12">
          <i-button icon="md-settings" shape="circle">设置</i-button>
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
      query: {},
      formItem: {},
      page: 0,
      arr: []
    };
  },
  created() {
    this.name = this.$route.params.name;
    this.getData();
  },
  mounted() {},
  methods: {
    reset() {
      for (let key in this.query) {
        this.query[key] = "";
      }
    },
    errors(e) {
      console.log(e);
    },
    sucess(e) {
      console.log(e);
    },
    search() {
      this.page = 0;
      this.getData();
    },
    getData(t) {
      let that = this;
      let data = this.query;
      data.limit = 10;
      data.offset = this.page * 10;
      this.$ajax({
        url: "daoru/member_index",
        data: this.query,
        then: r => {
          r.data.data.forEach(val => {
            val.visible = false;
          });
          if (t) {
            that.arr = that.arr.concat(r.data.data);
          } else {
            that.arr = r.data.data;
          }
        }
      });
    },
    edit() {},
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

