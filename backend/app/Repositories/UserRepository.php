<?php

    namespace App\Repositories;

    use App\Models\User;
    use App\Models\UserProp;
    use Illuminate\Support\Facades\Schema;

    class UserRepository
    {

        protected $userId;

        public function __construct()
        {

        }

        /**
         *
         * @param array $filters
         * @return \Illuminate\Database\Eloquent\Collection
         */
        public function get(array $filters = [])
        {
            $query = User::query();

            foreach ($filters as $field => $value) {

                if (Schema::hasColumn('users', $field)) {
                    if (is_array($value)) {
                        $query->whereIn($field, $value);
                    } elseif (is_string($value)) {
                        $query->where($field, 'like', '%' . $value . '%');
                    } else {
                        $query->where($field, $value);
                    }
                }
            }

            return $query->get();
        }


        /**
         *
         * @param int $userId
         * @param string $name
         * @param mixed $value
         * @param bool $force
         * @return void
         */
        public function setProp(string $name, $value, int $userId = null, bool $force = false): UserProp
        {
            if (!$userId) $userId = $this->userId;
            $query = UserProp::where('user_id', $userId)->where('name', $name);

            if ($force) {
                $query->delete();
            }

            $existingProp = $query->first();

            if ($existingProp && !$force) {
                $existingValue = json_decode($existingProp->value, true);
                if (is_array($existingValue)) {
                    $existingValue[] = $value;
                    $existingProp->update(['value' => json_encode($existingValue)]);
                    return $existingProp;
                } else {
                    // Если существующее значение строка, заменяем его
                    $existingProp->value = $value;
                    $existingProp->save();
                    return $existingProp;
                }
            } else {
                $prop = UserProp::create([
                    'user_id' => $userId,
                    'name' => $name,
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);

                return $prop;
            }
        }

        /**
         *
         * @param int $userId
         * @param string $name
         * @return mixed|null
         */
        public function getProp(string $name, int $userId = null)
        {
            if (!$userId) $userId = $this->userId;

            $prop = UserProp::where('user_id', $userId)->where('name', $name)->first();

            if ($prop) {
                $value = json_decode($prop->value, true);
                return json_last_error() === JSON_ERROR_NONE ? $value : $prop->value;
            }

            return null;
        }

        public function setUserId($userId) {
            $this->userId = $userId;
        }
    }
