<?php
namespace App\Http\Controllers\Last;

use App\CheckIn1;
use App\Http\Controllers\Controller;
use App\Integral;
use App\Integral1;
use App\Users1;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Location;

class UserController extends Controller
{
    /**
     * 用户注册
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function Register(Request $request)
    {
        if($request->isMethod('post')) {
            $uname = $request->uname;
            $pwd = $request->pwd;
            $re_pwd = $request->re_pwd;
            if( !$this->CheckData($uname, $pwd, $re_pwd) ) {
                echo '<script>alert("用户信息不合法")</script>';
                return view('last.reg');
            }else {
                if( $this->Add($uname, $pwd)) {
                    return redirect('login');
                }
            }
        }else {
            return view('last.reg');
        }
    }

    /**
     * 用户登录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function Login(Request $request)
    {
        if($request->isMethod('post')) {
            $uname = $request->uname;
            $pwd = $request->pwd;
            if(!$uname || !$pwd) {
                echo '<script>alert("用户信息不合法")</script>';
                return view('last.login');
            }
            if(!$res = $this->UserInfo($uname, $pwd)) {
                echo '<script>alert("用户信息错误")</script>';
                return view('last.login');
            }
            session(['uid' => $res->id]);
            return redirect('main');
        }else {
            return view('last.login');
        }
    }

    /**
     * 个人中心
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Main()
    {
        $User = new Users1();
        //获取用户信息
        $uid = session('uid');
        $info = $User->where('id', $uid)->first()->toArray();
        return view('last.main' , $info);
    }

    /**
     * 用户退出
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function UserOut(Request $request)
    {
        $uid = session('uid');
        $request->session()->forget('uid');
        $User = new Users1();
        $info = $User->where('id',$uid)->first();
        $this->Integral($uid,2, '退出扣除积分',15, $info['integral']);
        return redirect('login');
    }

    /**
     * 签到
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function CheckIn()
    {
        $uid = session('uid');
        $CkeckIn = new CheckIn1();
        if($res = $CkeckIn->where('uid', $uid)->orderBy('time','desc')->first()) {
            if(time() - strtotime($res['time']) > 3600*24) {
                $CkeckIn->insert([
                    'uid' => $uid
                ]);
            }else {
                echo '<script>alert("已签到");location.href="main"</script>';
                die;
            }
        }else {
            $CkeckIn->insert([
                'uid' => $uid
            ]);
        }
        $User = new Users1();
        $info = $User->where('id', $uid)->first();
        $this->Integral($uid, 1, '每日签到', '35', $info['integral']);
        echo '<script>alert("签到成功");location.href="main"</script>';
        die;
    }

    /**
     * 用户完善信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function AddInfo(Request $request)
    {
        if($request->isMethod('post')) {
            $phone = $request->phone;
            $email = $request->email;
            $User = new Users1();
            $uid = session('uid');
            $info = $User->where('id', $uid)->first();
            $this->Integral($uid,1, '个人信息完善（邮箱，手机等）',15, $info['integral']);
            $User->where('id', $uid)->update([
                'phone' => $phone,
                'email' => $email,
            ]);
            echo '<script>alert("保存成功");location.href="main"</script>';
            die;
        } else {
            return view('last.add');
        }
    }

    /**
     * 积分 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|int
     */
    public function IntegralList(Request $request)
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $uid = session('uid');
        $type = $request->type;
        if(empty($type)) {
            if($info = $redis->hGet('User' , 'User::'.$uid.'all')) {
                $data = json_decode($info,true);
            }else {
                $Integral = new Integral1();
                $data = $Integral->where('uid', $uid )
                    ->get();
                $redis->hSet('User', 'User::'.$uid.'all', json_encode($data));
            }
        }else {
            if($info = $redis->hGet('User' , 'User::'.$uid.$type)) {
                $data = json_decode($info,true);
            }else {
                $Integral = new Integral1();
                $data = $Integral->where('uid', $uid )
                    ->where('type' , $type)
                    ->get();
                $redis->hSet('User', 'User::'.$uid.$type, json_encode($data));
            }
        }
        return view('last.integral' ,['data' => $data]);
    }

    /**
     * 判断登录
     * @param $uname
     * @param $pwd
     * @return Users1|bool|\Illuminate\Database\Eloquent\Model|null
     */
    public function UserInfo($uname, $pwd)
    {
        $User = new Users1();
        if( $info = $User
            ->where('uname', $uname)
            ->where('pwd', md5($pwd))
            ->first()
        )
        {
            $this->Integral($info['id'] , '1', '登录送积分', 25, $info['integral']);
            return $info;
        }
        return false;
    }

    /**
     * 添加入库
     * @param $uname
     * @param $pwd
     * @return bool
     */
    public function Add($uname, $pwd)
    {
        $User = new Users1();
        $res = $User->insertGetId([
            'uname' => $uname,
            'pwd' => md5($pwd),
        ]);
        if($res) {
            $this->Integral($res , '1', '注册加积分', 150);
            return true;
        }else {
            return false;
        }
    }

    /**
     * 操作积分表
     * @param $uid
     * @param $type
     * @param $doing
     * @param $integral
     * @return bool
     */
    public function Integral($uid , $type, $doing, $integral, $sum = 0)
    {
        $Integral = new Integral1();
        $Integral->insert([
            'uid' => $uid,
            'type' => $type,
            'doing' => $doing,
            'integral_change' => intval($integral),
        ]);
        $User =new Users1();
        $User->where('id' , $uid)->update([
            'integral' => $type == 1 ? $sum + $integral : $sum - $integral
        ]);
        return true;
    }

    /**
     * 验证 用户信息
     * @param $uname
     * @param $pwd
     * @param $re_pwd
     * @return bool
     */
    public function CheckData($uname, $pwd, $re_pwd = 0)
    {
        if(!$uname || !$pwd || !$re_pwd) {
            return false;
        }
        if($pwd != $re_pwd) {
            return false;
        }
        $User = new Users1();
        if($User->where('uname', $uname)->first()) {
            return false;
        }
        return true;
    }
}
