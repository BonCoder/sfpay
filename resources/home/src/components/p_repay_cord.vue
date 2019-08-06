<template>
  <div @scroll="scroll($event)">
    <i-form inline v-show="show && !showComponents &&!status">
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
      <i-button
        type="ghost"
        size="large"
        @click="showComponents =true"
        accept="xlsx"
        style="color:green"
      >导入EXCEL文件</i-button>
    </i-form>
    <Uploads @success="sucess" @errors="errors" v-show="showComponents"></Uploads>
    <Card v-for="(item,i) in arr" :key="i" v-show="!showComponents&!status">
      <Row>
        <i-col class="tables" span="4">
          <span>批次</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.picihao}}</span>
        </i-col>
        <i-col class="tables" span="4">
          <span>用户</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.account}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="4">
          <span>公司</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.nickname}}</span>
        </i-col>
        <i-col class="tables" span="4">
          <span>名称</span>
        </i-col>
        <i-col class="tables" span="10">
          <span>{{item.filename}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="4">
          <span>笔数</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.count}}</span>
        </i-col>
        <i-col class="tables" span="4">
          <span>金额</span>
        </i-col>
        <i-col class="tables" span="8">
          <span>{{item.money}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="4">
          <span>日期</span>
        </i-col>
        <i-col class="tables" span="12">
          <span>{{item.create_time}}</span>
        </i-col>
      </Row>
      <div class="ivu-col box time">
        <i-button size="lager" type="success" @click="see(i)">
          <Icon type="ios-paper-plane-outline" />
          <span>查看</span>
        </i-button>
      </div>
    </Card>
    <Card v-show="status" v-for="(props,index) in seeList " :key="index" >
      <div class="ivu-col box" v-for="(item,idx) in props " :key="idx" v-show="!item.time">
        <div>
          <span>{{item.name}}</span>
          <span>{{item.value}}</span>
        </div>
      </div>
    </Card>
    <i-button type="error" v-show="status" @click="status=false" style="margin-top:50px">
      <Icon type="ios-undo" />返回
    </i-button>
    <!-- <Page :total="40" size="small" show-total></Page> -->
  </div>
</template>
<script>
import Uploads from "./opload";
export default {
  props: ["show"],
  components: { Uploads },
  data() {
    return {
      visible: false,
      name: "",
      query: {},
      formItem: {},
      page: 0,
      arr: [],
      showComponents: false,
      seeList: [],
      status: false
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
      this.showComponents = false;
    },
    sucess(e) {
      this.$Message.success("上传成功");
      this.showComponents = false;
    },
    search() {
      this.page = 0;
      this.getData();
    },
    see(i) {
      let that = this;
      this.$ajax({
        url: "daoru/detail",
        data: { id: this.arr[i].id },
        then: r => {
          if (r.data.length == 0) {
            that.$Message.info("没有更多数据");
            return false;
          }
          r.data.forEach((val, i) => {
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
              case 6:
                str = "转账成功";
                break;
              case 5:
                str = "代付成功";
                break;
            }

            let arr = [
              { name: "账号", value: val.account },
              { name: "公司名称", value: val.nickname },
              { name: "代付金额", value: val.money },
              { name: "身份证号", value: val.shenfenzheng, check: true },
              { name: "户名", value: val.bank_owner, check: true },
              { name: "卡号", value: val.bank_card, check: true },
              { name: "日期", value: val.create_time },
              { name: "状态	", value: str }
            ];
            that.seeList.push(arr);
          });
        }
      });
      this.status = true;
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
          r.data.forEach(val => {
            val.visible = false;
          });
          if (t) {
            that.arr = that.arr.concat(r.data);
          } else {
            that.arr = r.data;
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

