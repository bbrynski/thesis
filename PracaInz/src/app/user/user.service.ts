import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { User } from '../shared/user';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private userUrl = 'http://localhost/repository/pracainz/PracaInzBackend/Uzytkownik';
  private isEditMode = false;
  private toEditUser: User;

  constructor(private http: HttpClient) { }

  getAllUser(): Observable<any[]> {
    return this.http.get<any[]>(this.userUrl);
  }

  getOneUser(index: number): Observable<any> {
    return this.http.get<any[]>(this.userUrl + '/' + index);
  }

  addUser(user: any, isEditMode: boolean): Observable<any> {
    if (isEditMode) {
      return this.http.post<any>(this.userUrl + '/edytuj', user, httpOptions);
    } else {
      return this.http.post<any>(this.userUrl + '/wstaw', user, httpOptions);
    }
  }

  deactivateUser(index: number): Observable<any[]> {
    return this.http.delete<any[]>(this.userUrl + '/dezaktywuj/' + index);
  }

  setEditUser(user: User) {
    this.toEditUser = user;
    this.changeEditMode();
  }
  getEditEmployee() {
    return this.toEditUser;
  }

  changeEditMode() {
    this.isEditMode = !this.isEditMode;
  }
  getIsEditMode() {
    return this.isEditMode;
  }

  getUserByUsername(user: string) {
    return this.http.get<any[]>(this.userUrl + '/' + user);
  }
}
