# Keycloak SSO 설정 가이드

이 문서는 라라벨 프로젝트에 Keycloak SSO를 설정하는 방법을 안내합니다.

## 1. 패키지 설치

프로젝트에 `robsontenorio/laravel-keycloak-guard` 패키지가 이미 설치되어 있습니다.

```bash
composer require robsontenorio/laravel-keycloak-guard
```

## 2. Keycloak 서버 설정

### 2.1 Keycloak 서버 준비
- Keycloak 서버가 실행 중이어야 합니다.
- Realm과 Client가 생성되어 있어야 합니다.

### 2.2 Client 설정
Keycloak Admin Console에서 다음 설정을 확인하세요:

1. **Client Protocol**: `openid-connect`
2. **Access Type**: `confidential`
3. **Valid Redirect URIs**: 라라벨 앱의 콜백 URL 추가
   - 예: `http://localhost:8000/callback`
   - 예: `http://localhost:8000/sso/callback`

## 3. 환경 변수 설정

`.env` 파일에 다음 환경 변수를 추가하세요:

```env
# Keycloak SSO Configuration
KEYCLOAK_BASE_URL=https://your-keycloak-server.com
KEYCLOAK_REALM=your-realm-name
KEYCLOAK_REALM_PUBLIC_KEY=your-realm-public-key
KEYCLOAK_CLIENT_ID=your-client-id
KEYCLOAK_CLIENT_SECRET=your-client-secret
KEYCLOAK_CACHE_OPENID=false
KEYCLOAK_REDIRECT_URI=http://localhost:8000/callback
KEYCLOAK_LEEWAY=60
```

### 환경 변수 설명

| 변수 | 설명 | 예시 |
|------|------|------|
| `KEYCLOAK_BASE_URL` | Keycloak 서버 URL | `https://keycloak.example.com` |
| `KEYCLOAK_REALM` | Keycloak Realm 이름 | `my-realm` |
| `KEYCLOAK_REALM_PUBLIC_KEY` | Realm의 Public Key | Keycloak Admin에서 확인 |
| `KEYCLOAK_CLIENT_ID` | Client ID | `laravel-app` |
| `KEYCLOAK_CLIENT_SECRET` | Client Secret | Keycloak Admin에서 확인 |
| `KEYCLOAK_CACHE_OPENID` | OpenID 설정 캐싱 여부 | `false` (개발 시), `true` (프로덕션) |
| `KEYCLOAK_REDIRECT_URI` | 인증 후 리다이렉트 URI | `http://localhost:8000/callback` |
| `KEYCLOAK_LEEWAY` | JWT 토큰 유효성 검사 여유 시간 (초) | `60` |

## 4. Realm Public Key 확인 방법

1. Keycloak Admin Console 로그인
2. 해당 Realm 선택
3. **Realm Settings** → **Keys** 탭 이동
4. **RS256** 알고리즘의 **Public Key** 복사
5. `.env`의 `KEYCLOAK_REALM_PUBLIC_KEY`에 붙여넣기

## 5. 인증 설정 확인

`config/auth.php`에 keycloak guard가 추가되어 있는지 확인하세요:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'keycloak' => [
        'driver' => 'keycloak',
        'provider' => 'users',
    ],
],
```

## 6. 사용 방법

### 6.1 SSO 테스트 페이지 접속

1. 브라우저에서 `http://localhost:8000` 접속
2. **시스템 관리** 섹션에서 **SSO 테스트** 카드 클릭
3. 또는 직접 `http://localhost:8000/sso/test` 접속

### 6.2 인증 흐름

1. **Keycloak으로 로그인** 버튼 클릭
2. Keycloak 로그인 페이지로 리다이렉트
3. 로그인 성공 시 설정한 Redirect URI로 돌아옴
4. SSO 테스트 페이지에서 인증 상태 및 사용자 정보 확인

### 6.3 코드에서 사용

```php
use Illuminate\Support\Facades\Auth;

// 인증 확인
if (Auth::guard('keycloak')->check()) {
    $user = Auth::guard('keycloak')->user();
    // 사용자 정보 사용
}

// 로그아웃
Auth::guard('keycloak')->logout();
```

## 7. 콜백 컨트롤러 생성 (필요 시)

Keycloak에서 인증 후 돌아올 콜백 엔드포인트를 생성해야 할 수 있습니다:

```php
// routes/web.php
Route::get('/callback', function (Request $request) {
    $code = $request->query('code');

    // 토큰 교환 로직 구현
    // Keycloak 패키지가 자동으로 처리할 수도 있음

    return redirect('/sso/test');
})->name('sso.callback');
```

## 8. 미들웨어 사용 (선택사항)

특정 라우트에 Keycloak 인증을 요구하려면:

```php
Route::middleware(['auth:keycloak'])->group(function () {
    Route::get('/protected', function () {
        return 'This is protected by Keycloak SSO';
    });
});
```

## 9. 트러블슈팅

### 인증 실패 시
1. `.env` 파일의 Keycloak 설정 확인
2. Keycloak Client의 Redirect URI 설정 확인
3. Realm Public Key가 올바른지 확인
4. 라라벨 로그 확인: `storage/logs/laravel.log`

### 토큰 만료 오류
- `KEYCLOAK_LEEWAY` 값을 증가시켜보세요 (예: 120)

### 네트워크 오류
- Keycloak 서버가 실행 중인지 확인
- 방화벽 설정 확인

## 10. 참고 자료

- [Laravel Keycloak Guard 공식 문서](https://github.com/robsontenorio/laravel-keycloak-guard)
- [Keycloak 공식 문서](https://www.keycloak.org/documentation)

## 11. 구현된 파일 목록

- `app/Livewire/Sso/Test/Livewire.php` - SSO 테스트 Livewire 컴포넌트
- `resources/views/700-page-sso/000-index.blade.php` - SSO 테스트 페이지 뷰
- `config/auth.php` - Keycloak guard 설정
- `routes/web.php` - SSO 테스트 라우트
- `.env.example` - 환경 변수 예시

## 12. 다음 단계

1. 실제 Keycloak 서버 정보로 `.env` 파일 설정
2. SSO 테스트 페이지에서 인증 테스트
3. 필요한 경우 콜백 컨트롤러 구현
4. 프로덕션 환경에서는 `KEYCLOAK_CACHE_OPENID=true` 설정
