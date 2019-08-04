<template>
  <div class="layout-content" @scroll="scroll($event)">
    <i-form inline v-show="!status&&show">
      <Form-item>
        <i-input :value.sync="formItem.input" placeholder="请输入文件名"></i-input>
      </Form-item>
      <Form-item>
        <i-input :value.sync="formItem.input" placeholder="请输入金额" number></i-input>
      </Form-item>
      <Form-item>
        <i-input :value.sync="formItem.input" placeholder="请输入总笔数" number></i-input>
      </Form-item>
      <Form-item>
        <i-input :value.sync="formItem.input" placeholder="请输入批次号" number></i-input>
      </Form-item>
      <Form-item>
        <DatePicker
          type="daterange"
          style="width:100%"
          placement="bottom-start"
          placeholder="请选择时间"
        ></DatePicker>
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
      <div class="ivu-col box time">
        <i-button size="lager" type="success" @click="see(index)">
          <Icon type="ios-paper-plane-outline" />
          <span>查看</span>
        </i-button>
      </div>
    </Card>
    <Card v-show="status" v-for="(props,index) in seeList " :key="index">
      <div class="ivu-col box" v-for="(item,idx) in props " :key="idx" v-show="!item.time">
        <div>
          <span>{{item.name}}</span>
          <span>{{item.value}}</span>
        </div>
      </div>
    </Card>
    <i-button type="error" v-show="status" @click="status=false">
      <Icon type="ios-undo" />返回
    </i-button>
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
      query: { limit: 10 },
      page: 0,
      arr: [
        { name: "批次编号", value: "184511234", check: true },
        { name: "账号", value: "company" },
        { name: "公司名称", value: "balance" },
        { name: "总金额", value: "bankCardName", check: true },
        { name: "总笔数", value: "card", check: true },
        { name: "文件名", value: "bank", check: true },
        { name: "上传日期", value: "status" }
        // { name: "上次操作时间	", value: "lastTime" }
      ],
      check: [
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
      formItem: {},
      seeList: []
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
    search() {
      (this.page = 0), (this.query.offset = 0);
      this.getData();
    },
    see(i) {
      // for (let i = 0; i < 2; i++) {
      //   this.seeList.push(this.check);
      // }
      let that = this
      this.$ajax({
        url:"daoru/detail",
        data:{id:this.list[i][0].id},
        then:r=>{
          console.log(r)
          if(r.data.length==0){
            that.$Message.info('没有更多数据')
            return false
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
              { name: "开户名", value: val.bank_owner, check: true },
              { name: "银行卡号", value: val.bank_card, check: true },
              { name: "上传日期", value: val.create_time },
              { name: "状态	", value: str }
            ];
           that.seeList.push(arr);
          });
        }
      })
      this.status = true;
    },
    getData(  t) {
      let that = this;
      this.$ajax({
        url: "daoru/lists",
        data: this.query,
        then: r => {
          if (r.code != 1) {
            this.$Message.info(r.data.msg);
          }
          let list = [];
          r.data.forEach((val, i) => {
            let arr = [
              { name: "批次编号", value: val.picihao, check: true,id:val.id },
              { name: "账号", value: val.account },
              { name: "公司名称", value: val.nickname },
              { name: "总金额", value: val.money, check: true },
              { name: "总笔数", value: val.count, check: true },
              { name: "文件名", value: val.filename, check: true },
              { name: "上传日期", value: val.create_time }
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
