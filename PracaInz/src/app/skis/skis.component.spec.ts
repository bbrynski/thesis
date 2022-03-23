import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SkisComponent } from './skis.component';

describe('SkisComponent', () => {
  let component: SkisComponent;
  let fixture: ComponentFixture<SkisComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SkisComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SkisComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
