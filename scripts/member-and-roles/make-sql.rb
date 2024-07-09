#!/usr/bin/env ruby

require 'json'
require 'date'
require 'securerandom'
require 'uri'

CHILDCARE_PRODUCTS = ['Children', 'Infant', 'Thu. <16', 'Fri. <16', 'Sat. <16', 'Sun. <16', 'Mon. <16', 'WkEnd <16']
FORBIDDEN_PRODUCTS = CHILDCARE_PRODUCTS + ['Teenager', 'Apocryphal']
ONLINE_PRODUCTS = ['Online Membership', 'Online Ticket']

def get_badge_no(raw_row)
  "\##{raw_row[:ticket_number].gsub(',', '')}"
end

def get_name(raw_row)
  badge_name = "#{raw_row[:badge]}".strip
  preferred_name = "#{raw_row[:preferred_name_first]} #{raw_row[:preferred_name_last]}".strip
  full_name = "#{raw_row[:first_name]} #{raw_row[:last_name]}".strip
  [badge_name, preferred_name, full_name].find {|s| !s.empty?}
end

def get_can_be_in_childcare(raw_row)
  if raw_row.key?(:dob) && !raw_row[:dob].nil? && !raw_row[:dob].empty?
    dob = Date.parse(raw_row[:dob])
    con_end = Date.new(2024, 8, 12)
    age = con_end.year - dob.year - ((con_end.month > dob.month || (con_end.month == dob.month && con_end.day >= dob.day)) ? 0 : 1)
    return age <= 10
  end
  return CHILDCARE_PRODUCTS.include?(raw_row[:'Products - product_id → List Name'])
end

def get_roles(member, childcare)
  roles = []
  if member[:is_online]
    roles << 'online'
  else
    roles << 'in-person'
  end
  roles << 'wsfs' if member[:is_wsfs_voter]
  if childcare
    roles << 'childcare'
  end
  roles
end

def escape_sql(str)
  # Rather than trusting we've managed to escape everything, reject anything suspicious.
  # Because we are running this interactively, we can then decide whether to allow the character
  # or find out how to escape it.
  allowlist = "a-zA-Z0-9 .-@+()-_\"éöÅ'ï�ëäüø”íÖńłò!âåóð#ŠůáćžúØŁźŚżśèŻÚÍßÉšÁ’ñ赵海虹č♂麦子丰Æō科幻布玛‘ç«»"
  if !str.match?(/^[#{allowlist}]*$/)
    raise "Suspicious string #{str}: " + str.scan(/[^#{allowlist}]/).uniq.join('') 
  end
  $stderr.puts "Warning: � found in #{str}" if str.include?('�')
  str.gsub("'", "''").gsub('�', '')
end

def escape_csv(str)
  str.gsub('"', '""')
end

def escape_url(str)
  URI.encode_www_form_component(str)
end

if ARGV.empty?
  puts "Usage: #{$0} [clyde_data.json] [sql_output.sql]"
  puts
  puts "clyde_data.json: JSON file containing Clyde data. From the output of the seed-member-portal report."
  puts "sql_output.sql: Path to the file to write the SQL output to."
  exit 1
end

clyde_data_path, sql_output_path = ARGV

clyde_data = JSON.parse(File.read(clyde_data_path), symbolize_names: true)
members = clyde_data.map do |clyde_row|
  {
    user_id: clyde_row[:user_id],
    badge_no: get_badge_no(clyde_row),
    email: clyde_row[:email],
    name: get_name(clyde_row),
    requests_child_care: clyde_row[:requests_child_care] == true,
    can_be_in_child_care: get_can_be_in_childcare(clyde_row),
    has_online_access: !FORBIDDEN_PRODUCTS.include?(clyde_row[:'Products - product_id → List Name']) && ['2', '3'].include?(clyde_row[:attending_status]),
    is_wsfs_voter: clyde_row[:wsfs_status] == '1',
    is_online: clyde_row[:attending_status] == '3',
  }
end

by_user_id = members.group_by { |m| m[:user_id] }
childcare_info = by_user_id.map do |user_id, user_members|
  [user_id, user_members.any? { |m| m[:requests_child_care] || m[:can_be_in_child_care] }]
end.to_h

online_members = members.filter_map do |member|
  if !member[:has_online_access]
    nil
  else
    {
      badge_no: member[:badge_no],
      email: member[:email],
      name: member[:name],
      roles: get_roles(member, childcare_info[member[:user_id]]),
    }
  end
end

File.open(sql_output_path, 'w') do |f_sql|
  f_sql.puts "START TRANSACTION;"

  online_members.each do |member|
    f_sql.puts "INSERT INTO members (badge_no, email, name) VALUES ('#{member[:badge_no]}', '#{escape_sql(member[:email])}', '#{escape_sql(member[:name])}');"
    member[:roles].each do |role|
      f_sql.puts "INSERT INTO member_roles (badge_no, role_id) SELECT '#{member[:badge_no]}', role_id FROM roles WHERE name = '#{escape_sql(role)}';"
    end
  end

  # Manually change to COMMIT in the output after confirming the script runs correctly.
  f_sql.puts "ROLLBACK;"
end
