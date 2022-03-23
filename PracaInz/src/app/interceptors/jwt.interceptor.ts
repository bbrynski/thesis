import { Injectable, Inject } from '@angular/core';
import {
  HttpEvent, HttpInterceptor, HttpHandler, HttpRequest
} from '@angular/common/http';
import { LOCAL_STORAGE, StorageService } from 'angular-webstorage-service';
import { Observable } from 'rxjs';

@Injectable()
export class JwtInterceptor implements HttpInterceptor {

  constructor(@Inject(LOCAL_STORAGE) private localStorage: StorageService) { }

  intercept(req: HttpRequest<any>, next: HttpHandler):
    Observable<HttpEvent<any>> {

      const currentUser = this.localStorage.get('currentUser');
      if (currentUser != null && currentUser['token'] != null) {
        req = req.clone({
          setHeaders: {
            Authorization: `Bearer ${currentUser['token']}`
          }
        });
    }

    return next.handle(req);
  }
}
