class Pelanggan extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'idpelanggan';
    protected $fillable = ['pelanggan', 'alamat', 'telp', 'email', 'password'];
    
    public function orders()
    {
        return $this->hasMany(Order::class, 'idpelanggan');
    }
}