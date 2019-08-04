<template>
  <div class="layout-content" @scroll="scroll($event)">
    <i-form inline v-show="!status&&show">
      <Form-item>
        <i-input v-model="query.member_id" placeholder="请输入用户ID"></i-input>
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
        <i-input v-model="query.bank_name" placeholder="请输入银行"></i-input>
      </Form-item>
      <i-button type="success" size="large" @click="search">
        <Icon type="ios-search"></Icon>
        <span>搜索</span>
      </i-button>
    </i-form>
    <Card v-show="!status" v-for="(props,index) in list " :key="index">
      <div class="ivu-col box" v-for="(item,idx) in props " :key="idx" v-show="!item.time">
        <div>
          <span>{{item.name}}</span>
          <span>{{item.value}}</span>
        </div>
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
      page: 0,
      arr: [
        { name: "序号", value: "184511234", check: true },
        { name: "账号", value: "company" },
        { name: "公司名称", value: "balance" },
        { name: "代付金额", value: "balance" },
        { name: "身份证号", value: "bankCardName", check: true },
        { name: "开户名", value: "card", check: true },
        { name: "银行卡号", value: "bank", check: true },
        { name: "上传日期", value: "status" },
        { name: "状态	", value: "初审通过" }
      ],
      query: {limit:10}
    };
  },
  created() {
    let that = this;
    setInterval(() => {
      let time = new Date();
      that.msg = time.toLocaleString();
    }, 1000);
    this.getData();
  },
  methods: {
    getData(t) {
      let that = this;
      this.$ajax({
        url: "daifu/lists",
        data: this.query,
        then: r => {
          if (r.code != 1) {
            this.$Message.info(r.data.msg);
          }
          let list = [];
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
              { name: "开户名", value: val.bank_owner, check: true },
              { name: "银行卡号", value: val.bank_card, check: true },
              { name: "上传日期", value: val.create_time },
              { name: "状态	", value: str }
            ];
            list.push(arr);
          });
          if (t) {
            that.list = that.list.concat(list);
          } else {
            that.list = list;
          }
        }
      });
    },
    search() {
      (this.page = 0), (this.query.offset = 0);
      this.getData();
    },
    scroll(e) {
      let [sH, sT, cH] = [
        e.currentTarget.scrollHeight,
        e.currentTarget.scrollTop,
        e.currentTarget.clientHeight
      ];
      if (sH == sT + cH) {
        this.page++;
        this.query.offset = 10 * this.page;
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
  /* display: inline-block; */
  margin: 0 auto;
  margin-bottom: 20px;
}
</style>
