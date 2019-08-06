<template>
  <div class="layout-content" @scroll="scroll($event)">
    <i-form inline v-show="show">
      <Form-item>
        <i-input v-model="admin_name" placeholder="请输入用户名"></i-input>
      </Form-item>
      <i-button type="success" size="large" @click="search">
        <Icon type="ios-search"></Icon>
        <span>搜索</span>
      </i-button>
    </i-form>
    <Card v-show="!status" v-for="(props,index) in list " :key="index">
      <div class="ivu-col box" v-for="(item,idx) in props " :key="idx" v-show="!item.time">
        <div>
          <span class="span-1">{{item.name}}</span>
          <span class="span-2">{{item.value}}</span>
        </div>
      </div>

      <i-button type="error" @click="del(props[0].id)">
        <Icon type="ios-trash" size="18"></Icon>
        <span>删除</span>
      </i-button>
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
      admin_name: "",
      list: [],
      page: 0,
      arr: [
        { name: "用户ID", value: "acount", check: true },
        { name: "用户", value: "company" },
        { name: "描述", value: "payParsent", check: true },
        { name: "操作IP", value: "balance" },
        { name: "状态", value: "status" },
        { name: "操作时间	", value: "lastTime" }
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
    del(id){
      let that =this
      this.$ajax({
        url:'log/del_log',
        method:"POST",
        data:{log_id:id},
        then:r=>{
          that.$Message.success(r.msg)
        }
      })
    },
    search(){
      this.page=0
      this.getData()
    },
    getData(t) {
      let that = this;
      this.$ajax({
        url: "log/lists",
        data: {
          admin_name: this.admin_name,
          offset: this.page * 10,
          limit: 10
        },
        then: r => {
          let list =[]
          r.data.forEach(val => {
            let arr = [
              { name: "用户ID", value:val.admin_id, check: true ,id:val.log_id},
              { name: "操作用户", value: val.admin_name    },
              { name: "描述", value:  val.description , check: true },
              { name: "操作IP", value:  val.ip },
              { name: "状态", value:  val.status },
              { name: "操作时间	", value:val.add_time}
            ];
            list.push(arr)
          });
          if(t){
            that.list = that.list.concat(list)
          }else{
            that.list = list
          }
        }
      });
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
