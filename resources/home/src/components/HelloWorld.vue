<template>
  <div class="layout-content">
    <Card shadow>
      <p class="text_over" slot="title">可用余额</p>
      <p class="text_over">
        {{info.money}}
        <span>元</span>
      </p>
    </Card>
    <Card shadow>
      <p class="text_over" slot="title">最近充值</p>
      <p class="text_over">
        {{info.chongzhi}}
        <span>元</span>
      </p>
    </Card>
    <Card shadow>
      <p class="text_over" slot="title">最近代付</p>
      <p class="text_over">
        {{info.daifu}}
        <span>元</span>
      </p>
    </Card>
    <Affix :offset-bottom="50">
      <span class="demo-affix">尊敬的用户,现在是 {{msg}}</span>
    </Affix>
  </div>
</template>

<script>
export default {
  name: "HelloWorld",
  data() {
    return {
      msg: "",
      info:{}
    };
  },
  created() {
    let that = this;
    setInterval(() => {
      let time = new Date();
      that.msg = time.toLocaleString();
    },1000);
    this.$emit('search',false)
    this.getData()
  },
  methods:{
    getData(t){
      let that = this
      this.$ajax({
        url:"chongzhi/home",
        then:r=>{
          that.info =r.data
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
  height: 100px;
  /* display: inline-block; */
  margin: 0 auto;
  margin-bottom: 20px;
}
</style>
