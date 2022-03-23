import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Employee } from '../shared/employee';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class EmployeeService {

  private employeeUrl = 'http://localhost/repository/pracainz/PracaInzBackend/Pracownik';
  private isEditMode = false;
  private toEditEmployee: Employee;
  constructor(private http: HttpClient) { }

  getAllEmployee(): Observable<any[]> {
    return this.http.get<any[]>(this.employeeUrl);
  }

  addEmployee(employee: any, isEditMode: boolean): Observable<any> {
    if (isEditMode) {
      return this.http.post<any>(this.employeeUrl + '/edytuj', employee, httpOptions);
    } else {
      return this.http.post<any>(this.employeeUrl + '/wstaw', employee, httpOptions);
    }
  }

  deactivateEmployee(index: number): Observable<any[]> {
    return this.http.delete<any[]>(this.employeeUrl + '/dezaktywuj/' + index);
  }

  getOneEmployee(index: number): Observable<any> {
    return this.http.get<any[]>(this.employeeUrl + '/' + index);
  }

  getEmployeeByUsername(user: string) {
    return this.http.get<any[]>(this.employeeUrl + '/' + user);
  }
  changeEditMode() {
    this.isEditMode = !this.isEditMode;
  }
  getIsEditMode() {
    return this.isEditMode;
  }
  setEditEmployee(employee: Employee) {
    this.toEditEmployee = employee;
    this.changeEditMode();
  }
  getEditEmployee() {
    return this.toEditEmployee;
  }
}
