use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;

Fortify::createUsersUsing(CreateNewUser::class);
