<template>
  <i-form>
    <Form-item label="原密码">
      <i-input v-model="formItem.old_password" placeholder="请输入密码" type="password"></i-input>
    </Form-item>
    <Form-item label="新密码">
      <i-input v-model="formItem.password" placeholder="请输入密码" type="password"></i-input>
    </Form-item>
    <!-- <Form-item label="重复密码">
      <i-input v-model="formItem.password" placeholder="请输入"></i-input>
    </Form-item>-->
    <i-button type="primary" @click="submit">提交</i-button>
    <i-button type="error" @click="reset">重置</i-button>
  </i-form>
</template>
<script>
export default {
  data() {
    return {
      formItem: {},
      loading2: false,
      isLogin: false
    };
  },
  created() {
    if (this.$route.params.name == "修改登陆密码") {
      this.isLogin = true;
    }
    this.$emit("search", false);
  },
  methods: {
    reset() {
      for (var key in this.formItem) {
        this.formItem[key] = "";
      }
    },
    submit() {
      console.log(1);
      this.$ajax({
        url: this.isLogin
          ? "member/updatePassword"
          : "member/updatePayPassword",
        data: this.formItem,
        method: "POST",
        then: r => {
          this.$Message.success("成功");
        }
      });
    }
  }
};
</script>
<style scoped>
</style>


