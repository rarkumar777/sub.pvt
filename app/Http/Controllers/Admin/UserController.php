<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Handle delete via URL parameter (reference pattern)
        if ($request->has('del')) {
            $delId = intval($request->del);
            if ($delId != auth()->id()) {
                User::destroy($delId);
                return redirect()->route('admin.users.index')->with('success', 'User deleted');
            }
        }

        $users = User::orderByDesc('id');

        if ($request->filled('country')) {
            $users->where('country', $request->country);
        }
        if ($request->filled('user_group')) {
            $users->where('user_group', $request->user_group);
        }
        if ($request->filled('email')) {
            $users->where('email', $request->email);
        }
        if ($request->filled('first_name')) {
            $users->where('first_name', 'like', '%' . $request->first_name . '%');
        }
        if ($request->filled('last_name')) {
            $users->where('last_name', 'like', '%' . $request->last_name . '%');
        }
        if ($request->filled('company')) {
            $users->where('company', 'like', '%' . $request->company . '%');
        }

        $users = $users->paginate(20);

        // Get distinct countries used by users
        $countryIds = \App\Models\User::whereNotNull('country')->where('country', '>', 0)->distinct()->pluck('country')->toArray();
        $countries = \App\Models\Country::whereIn('id', $countryIds)->pluck('name', 'id')->toArray();

        // User groups list
        $userGroups = ['admin', 'pv travels team', 'pv old team', 'supplier', 'clients', 'Partners', 'nomad', 'agents', 'employees', 'provider'];

        // Migrate any 'guest' users to 'provider'
        User::where('user_group', 'guest')->update(['user_group' => 'provider']);

        return view('admin.users.index', compact('users', 'countries', 'userGroups'));
    }

    public function create()
    {
        $countries = \App\Models\Country::orderBy('name')->pluck('name', 'id')->toArray();
        $userGroups = ['admin', 'pv travels team', 'pv old team', 'supplier', 'clients', 'Partners', 'nomad', 'agents', 'employees', 'provider'];
        return view('admin.users.create', compact('countries', 'userGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:en33_users,email',
            'password' => 'required|min:8',
        ]);

        if ($request->password !== $request->retype_password) {
            return redirect()->back()->withErrors(['Password and retype password do not match'])->withInput();
        }

        $salt = 'asdajs';
        $data = $request->only([
            'first_name', 'last_name', 'email', 'url', 'country', 'city',
            'company', 'mobile', 'phone', 'fax', 'address', 'birth_day',
            'gender', 'user_group'
        ]);
        // Handle null/empty values: date fields get null, int fields get 0, string fields get empty string
        $dateFields = ['birth_day'];
        $intFields = ['country', 'gender'];
        foreach ($data as $key => $value) {
            if (in_array($key, $dateFields)) {
                // Validate it's a real date, not placeholder text like 'YYYY-DD-MM'
                $data[$key] = (!empty($value) && strtotime($value) !== false) ? $value : null;
            } elseif (in_array($key, $intFields)) {
                $data[$key] = (!empty($value)) ? (int)$value : 0;
            } elseif (is_null($value)) {
                $data[$key] = '';
            }
        }

        $data['pass'] = sha1($salt . sha1($request->password));
        $data['user_regdate'] = time();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $ext = strtolower($file->getClientOriginalExtension());
            $avatarName = sha1($request->email) . '.' . $ext;
            $file->move(public_path('uploads/avatars'), $avatarName);
            $data['avatar'] = url('uploads/avatars/' . $avatarName);
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $countries = \App\Models\Country::orderBy('name')->pluck('name', 'id')->toArray();
        $userGroups = ['admin', 'pv travels team', 'pv old team', 'supplier', 'clients', 'Partners', 'nomad', 'agents', 'employees', 'provider'];
        return view('admin.users.edit', compact('user', 'countries', 'userGroups'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:en33_users,email,' . $id,
        ]);

        $data = $request->only([
            'first_name', 'last_name', 'email', 'url', 'country', 'city',
            'company', 'mobile', 'phone', 'fax', 'address', 'birth_day',
            'gender', 'user_group'
        ]);

        // Handle null/empty values: date fields get null, string fields get empty string, int fields get 0
        $dateFields = ['birth_day'];
        $intFields = ['country', 'gender'];
        foreach ($data as $key => $value) {
            if (in_array($key, $dateFields)) {
                // Validate it's a real date, not placeholder text like 'YYYY-DD-MM'
                $data[$key] = (!empty($value) && strtotime($value) !== false) ? $value : null;
            } elseif (in_array($key, $intFields)) {
                $data[$key] = (!empty($value)) ? (int)$value : 0;
            } elseif (is_null($value)) {
                $data[$key] = '';
            }
        }

        if ($request->filled('password')) {
            if ($request->password !== $request->retype_password) {
                return redirect()->back()->withErrors(['password' => 'Password and retype password do not match'])->withInput();
            }
            $salt = 'asdajs';
            $data['pass'] = sha1($salt . sha1($request->password));
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $ext = strtolower($file->getClientOriginalExtension());
            $avatarName = sha1($user->email) . '.' . $ext;
            $file->move(public_path('uploads/avatars'), $avatarName);
            $data['avatar'] = url('uploads/avatars/' . $avatarName);
        }

        $user->update($data);
        return redirect()->route('admin.users.edit', $id)->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }

    public function show($id)
    {
        return $this->edit($id);
    }

    public function groups(Request $request)
    {
        $configPath = base_path('../pvt.jo/config/users/user_groups.php');
        $userGroups = $this->parseUserGroups($configPath);

        // Handle delete
        if ($request->has('del')) {
            $delName = $request->del;
            $usersInGroup = User::where('user_group', $delName)->count();
            if ($usersInGroup == 0 && isset($userGroups[$delName])) {
                unset($userGroups[$delName]);
                $this->saveUserGroups($configPath, $userGroups);
                return redirect()->route('admin.user-groups.index')->with('success', 'Group deleted');
            }
            return redirect()->route('admin.user-groups.index')->withErrors(['Cannot delete this group (users exist or is default)']);
        }

        return view('admin.users.groups', compact('userGroups'));
    }

    public function storeGroup(Request $request)
    {
        $configPath = base_path('../pvt.jo/config/users/user_groups.php');
        $userGroups = $this->parseUserGroups($configPath);

        $groupName = strtolower(trim($request->input('group_name', '')));
        $action = $request->input('action', 'add_new');

        // Reject empty group names
        if (empty($groupName)) {
            return redirect()->route('admin.user-groups.index')->withErrors(['Group name is required']);
        }

        if ($action === 'add_new' && isset($userGroups[$groupName])) {
            return redirect()->route('admin.user-groups.index')->withErrors(['Group name already in use']);
        }

        // If editing and name changed, update users table and remove old
        if ($action === 'edit') {
            $editName = $request->input('edit_name');
            if ($editName !== $groupName && !isset($userGroups[$groupName])) {
                User::where('user_group', $editName)->update(['user_group' => $groupName]);
                unset($userGroups[$editName]);
            }
        }

        $groupType = intval($request->input('group_type', 0));
        $price = ($groupType == 0) ? 0 : intval($request->input('group_price', $request->input('group_price_hidden', 0)));
        $valid = ($groupType == 0) ? 0 : intval($request->input('group_cycle', $request->input('group_cycle_hidden', 0)));

        $userGroups[$groupName] = [
            'type' => $groupType,
            'price' => $price,
            'valid' => $valid,
            'allowed' => intval($request->input('in_use', 0)),
            'activate_by' => $request->input('group_activate', 'email'),
        ];

        $this->saveUserGroups($configPath, $userGroups);
        return redirect()->route('admin.user-groups.index')->with('success', 'Group saved successfully');
    }

    private function parseUserGroups($configPath)
    {
        $GOGIES = [];
        if (file_exists($configPath)) {
            // Invalidate OPcache to ensure fresh file is read
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($configPath, true);
            }
            if (!defined('gogies')) {
                define('gogies', true);
            }
            include $configPath;
        }
        return $GOGIES['user_groups'] ?? [];
    }

    private function saveUserGroups($configPath, $groups)
    {
        $data = '<?php if (!defined("gogies")){die("direct script access is not allowed");} ';
        foreach ($groups as $k => $v) {
            $v['allowed'] = intval($v['allowed'] ?? 0);
            $data .= ' $GOGIES[\'user_groups\'][\'' . $k . '\']=array(\'type\'=>' . $v['type'] . ' , \'price\'=>' . $v['price'] . ', \'valid\'=>' . $v['valid'] . ' , \'allowed\'=>' . $v['allowed'] . ', \'activate_by\'=>\'' . $v['activate_by'] . '\');';
        }
        // Ensure directory exists before writing
        $dir = dirname($configPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($configPath, $data);
        // Invalidate OPcache so next read gets fresh data
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($configPath, true);
        }
    }

    public function permissions($userId)
    {
        $user = User::findOrFail($userId);
        $userPerms = [];
        if (!empty($user->permission)) {
            $perms = @unserialize($user->permission, ['allowed_classes' => false]);
            if (is_array($perms)) {
                $userPerms = $perms;
            }
        }
        return view('admin.users.permissions', compact('user', 'userPerms'));
    }

    public function updatePermissions(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $postData = $request->except(['_token']);
        $userPerm = [];
        foreach ($postData as $pk => $pv) {
            $userPerm[$pk] = intval($pv);
        }
        $user->update(['permission' => serialize($userPerm)]);
        return redirect()->route('admin.users.permissions', $userId)->with('success', 'Permissions updated');
    }

    public function groupFields($groupId)
    {
        $configPath = base_path('../pvt.jo/config/users/groups/' . $groupId . '.php');
        $fields = $this->parseGroupFields($configPath, $groupId);
        
        $fieldLabels = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'E-mail',
            'url' => 'Url',
            'country' => 'Country',
            'city' => 'City',
            'company' => 'Company',
            'mobile' => 'Mobile',
            'phone' => 'Telephone',
            'fax' => 'Fax',
            'address' => 'Address',
            'birth_day' => 'Birth Day',
            'gender' => 'Gender',
            'avatar' => 'Avatar'
        ];

        return view('admin.users.group-fields', compact('groupId', 'fields', 'fieldLabels'));
    }

    public function updateGroupFields(Request $request, $groupId)
    {
        $configPath = base_path('../pvt.jo/config/users/groups/' . $groupId . '.php');
        $postFields = $request->input('fields', []);
        
        $data = '<?php if (!defined(\'gogies\')){ exit;} ';
        foreach ($postFields as $key => $val) {
            // Sanitize key and value
            $key = preg_replace('/[^a-z0-9_]/', '', $key);
            $val = in_array($val, ['r', 'a', 'd']) ? $val : 'd';
            $data .= '$GOGIES[\'uf\'][\'' . $groupId . '\'][\'' . $key . '\']=\'' . $val . '\'; ';
        }
        
        // Ensure directory exists before writing
        $dir = dirname($configPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($configPath, $data);
        
        return redirect()->route('admin.user-groups.fields', $groupId)->with('success', 'Group fields updated successfully');
    }

    private function parseGroupFields($configPath, $groupId)
    {
        $GOGIES = [];
        if (file_exists($configPath)) {
            if (!defined('gogies')) {
                define('gogies', true);
            }
            include $configPath;
        }
        return $GOGIES['uf'][$groupId] ?? [];
    }

    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $action = $request->input('action', 'a');
        $user->update(['status' => ($action === 'a') ? 1 : 0]);
        return response()->json(['success' => true]);
    }
}
