import { Injectable, Inject } from '@angular/core';
import { Observable, BehaviorSubject } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { LOCAL_STORAGE, StorageService } from 'angular-webstorage-service';
import { API } from '../../environments/environment';
const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class AccessService {
  currentUser: string;

  admin = new BehaviorSubject<boolean>(false);
  employee = new BehaviorSubject<boolean>(false);
  owner = new BehaviorSubject<boolean>(false);

  isAdmin = this.admin.asObservable();
  isEmployee = this.employee.asObservable();
  isOwner = this.owner.asObservable();

  constructor(private http: HttpClient, @Inject(LOCAL_STORAGE) private localStorage: StorageService) { }

  setIsAdmin(value: boolean) {
    this.admin.next(value);
  }
  setIsEmployee(value: boolean) {
    this.employee.next(value);
  }
  setIsOwner(value: boolean) {
    this.owner.next(value);
  }

  login(data: any): Observable<any> {
      return this.http.post<any>(API + 'dostep/logowanie', data, httpOptions);
  }

  checkLocalStorage() {
    this.currentUser = this.localStorage.get('currentUser');
    if (this.currentUser && this.currentUser['token'] && this.currentUser['user'] && this.currentUser['principle']) {
      if (this.currentUser['principle'] === 'Administrator') {
        this.setIsAdmin(true);
      } else if (this.currentUser['principle'] === 'Pracownik') {
        this.setIsEmployee(true);
      }  else if (this.currentUser['principle'] === 'Właściciel') {
        this.setIsOwner(true);
      }
    } else {
      this.setIsAdmin(false);
      this.setIsEmployee(false);
      this.setIsOwner(false);
    }
  }
}
