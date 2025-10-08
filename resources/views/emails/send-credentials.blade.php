<h2>Ciao {{ $name }},</h2>
<p>Di seguito trovi le tue credenziali di accesso al gestionale Tecnolab:</p>
<ul>
    <li><strong>Username:</strong> {{ $username }}</li>
    <li><strong>Email:</strong> {{ $email }}</li>
    @if(!empty($password))
        <li><strong>Password:</strong> {{ $password }}</li>
    @else
        <li><strong>Password:</strong> Non disponibile (contatta l'amministratore se necessario)</li>
    @endif
</ul>
<p>Puoi accedere al gestionale da <a href="{{ url('/') }}">{{ url('/') }}</a></p>
<p>Se non hai richiesto questa email, puoi ignorarla.</p>
<p>Saluti,<br>Staff Tecnolab</p>

