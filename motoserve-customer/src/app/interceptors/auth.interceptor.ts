import { HttpInterceptorFn } from '@angular/common/http';
import { Preferences } from '@capacitor/preferences';
import { from, switchMap } from 'rxjs';

export const authInterceptor: HttpInterceptorFn = (request, next) => {
  const apiUrl = 'http://127.0.0.1:8000/api/customer/';

  if (
    !request.url.startsWith(apiUrl) ||
    request.url.endsWith('/login') ||
    request.url.endsWith('/register')
  ) {
    return next(request);
  }

  return from(
    Preferences.get({ key: 'motoserve_customer_token' })
  ).pipe(
    switchMap(({ value }) => {
      const authorizedRequest = value
        ? request.clone({
            setHeaders: {
              Authorization: `Bearer ${value}`,
            },
          })
        : request;

      return next(authorizedRequest);
    })
  );
};