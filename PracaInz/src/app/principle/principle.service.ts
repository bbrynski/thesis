import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Principle } from '../shared/principle';
import { Observable } from 'rxjs';
@Injectable({
  providedIn: 'root'
})
export class PrincipleService {
  private urlPrinciple = 'http://localhost/repository/pracainz/PracaInzBackend/Prawo';
  constructor(private http: HttpClient) { }

  getAllPrinciple(): Observable<Principle[]> {
    return this.http.get<Principle[]>(this.urlPrinciple);
  }
}
