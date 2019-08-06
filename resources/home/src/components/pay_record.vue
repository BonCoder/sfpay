<template>
  <div @scroll="scroll($event)">
    <i-form inline v-show="show">
      <Form-item>
        <DatePicker
          type="date"
          placeholder="开始时间"
          format="yyyy-MM-dd"
          style="width: 150px;vertical-align:middle"
          v-model="start"
        ></DatePicker>
      </Form-item>
      <Form-item>
        <DatePicker
          type="date"
          placeholder="结束时间"
          format="yyyy-MM-dd"
          style="width: 150px"
          v-model="end"
        ></DatePicker>
      </Form-item>
      <Form-item>
        <i-select placeholder="请选择类型" ref="select" @on-change="chage" clearable>
          <Option v-for="(item,i) in options" :key="i" :value="i">{{item}}</Option>
        </i-select>
      </Form-item>
      <i-button type="success" size="large" @click="search">查询</i-button>
      <i-button type="default" size="large" @click="reset">重置</i-button>
    </i-form>

    <Card v-for="(item,i) in arr" :key="i">
      <Row>
        <i-col class="tables" span="4">
          <span>公司</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.nickname}}</span>
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
          <span>类型</span>
        </i-col>
        <i-col class="tables" span="6">
          <span>{{item.type}}</span>
        </i-col>
        <i-col class="tables" span="4">
          <span>备注</span>
        </i-col>
        <i-col class="tables" span="8">
          <span>{{item.beizhu}}</span>
        </i-col>
      </Row>
      <Row>
        <i-col class="tables" span="4">
          <span>时间</span>
        </i-col>
        <i-col class="tables" span="11">
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
      clearable: false,
      visible: false,
      name: "",
      arr: [],
      page: 0,
      start: "",
      end: "",
      options: [
        "充值",
        "代付不成功返还",
        "手续费",
        "代付支出",
        "转账",
        "接口记录"
      ],
      type: ""
    };
  },
  created() {
    this.name = this.$route.params.name;
    this.arr.forEach(val => {
      val.visible = false;
    });
    this.getData();
  },
  mounted() {},
  methods: {
    getData(t) {
      let that = this;
      this.$ajax({
        url: "chongzhi/lists",
        data: {
          start: this.start ? new Date(this.start).toLocaleDateString() : "",
          end: this.end ? new Date(this.end).toLocaleDateString() : "",
          type: this.type,
          limit: 10,
          offset: this.page * 10
        },
        then: r => {
          if (t) {
            that.arr = that.arr.concat(r.data);
          } else {
            that.arr = r.data;
          }
        }
      });
    },
    search() {
      this.page = 0;
      this.getData();
    },
    reset() {
      this.end = "";
      this.start = "";
      this.type = "";
      this.$refs.select.clearSingleSelect();
    },
    chage(e) {
      this.type = e;
    },
    edit() {},
    scroll(e) {
      let [sH, sT, cH] = [
        e.currentTarget.scrollHeight,
        e.currentTarget.scrollTop,
        e.currentTarget.clientHeight
      ];
      if (sH == sT + cH) {
        this.page++;
        this.getData(true);
      }
    }
  }
};
</script>
<style >
.tables:not(:last-child) {
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

