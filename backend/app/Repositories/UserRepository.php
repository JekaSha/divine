    <?php

    namespace App\Repositories;

    use App\Models\User;
    use App\Models\UserProp;

    class UserRepository
    {

        protected $userId;

        public function __construct($userId)
        {

            $this->userId = $userId;

        }

        /**
         *
         * @param array $filters
         * @return \Illuminate\Database\Eloquent\Collection
         */
        public function get(array $filters = [])
        {
            $query = User::query();

            if (!empty($filters['name'])) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            }

            if (!empty($filters['email'])) {
                $query->where('email', $filters['email']);
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
        public function setProp(string $name, $value, int $userId = null, bool $force = false): void
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
                } else {
                    // Если существующее значение строка, заменяем его
                    $existingProp->update(['value' => $value]);
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
    }
